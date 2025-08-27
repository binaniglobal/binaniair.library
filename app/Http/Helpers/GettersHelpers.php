<?php

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


function getDomainName()
{
    return '@binaniair.com';
}

function countManualItemsById($manual_item_id)
{
    return ManualItemContent::where('manual_items_uid', $manual_item_id)->count();
}

function getManualById($id)
{
    return Manuals::where('mid', $id)->first();
}

function getManualItemById($manual_uid, $manual_item_uid)
{
    return ManualsItem::where('manual_uid', $manual_uid)->where('miid', $manual_item_uid)->where('file_type', 'application/pdf')->first();
}

function getManualItemsFolderById($manual_uid, $manual_item_uid)
{
    return ManualsItem::where('manual_uid', $manual_uid)->where('miid', $manual_item_uid)->where('file_type', 'Folder')->first();
}

function getManualContentById($manual_uid, $manual_item_uid, $manual_item_content_uid)
{
    return ManualItemContent::where('manual_uid', $manual_uid)->where('manual_items_uid', $manual_item_uid)->where('micd', $manual_item_content_uid)->first();
}

function giveAdminsAllPermissions($permission)
{
    $users = User::role(['super-admin', 'SuperAdmin', 'Admin'])->get();
    foreach ($users as $user) {
        // Assign the permission to each user
        $user->givePermissionTo($permission);
    }
}

function getParentManual($id)
{
    $manual = Manuals::where('mid', $id)->first();

    return $manual;
}

function deleteManualItemRecursively($manual_uid)
{
    if (!empty($manual_uid)) {
        $manual = Manuals::where('mid', $manual_uid)->first();
        $manual_item = ManualsItem::where('manual_uid', $manual_uid)->get();

        foreach ($manual_item as $item) {
            $manual_item_content = ManualItemContent::where('manual_uid', $manual_uid)->where('manual_items_uid', $item->miid)->get();
            foreach ($manual_item_content as $items) {
                if ($items->file_type != 'Folder') {
                    if (Storage::disk('privateSubManualContent')->exists($items->link)) {
                        Storage::disk('privateSubManualContent')->delete($items->link);
                    }
                }
                $permissionName = "access-manual-{$manual->name}.{$item->name}.{$items->name}";
                removePermissionFromAll($permissionName);
                $items->delete();
            }
            if ($item->file_type != 'Folder') {
                if (Storage::disk('privateSubManual')->exists($item->link)) {
                    Storage::disk('privateSubManual')->delete($item->link);
                }
            }
            $permissionName = "access-manual-{$manual->name}.{$item->name}";
            removePermissionFromAll($permissionName);
            $item->delete();
        }
        $permissionName = "access-manual-{$manual->name}";
        removePermissionFromAll($permissionName);
        $manual->delete();
    }
}

function removePermissionFromAll($permissionName)
{
    $permission = Permission::findByName($permissionName);
    // Or use name if easier
    // $permission = Permission::where('name', $permissionName)->firstOrFail();

    // 1. Remove direct permission from all users
    User::permission($permission->name)->get()->each(function ($user) use ($permission) {
        if ($user->hasDirectPermission($permission)) {
            $user->revokePermissionTo($permission);
        }
    });

    // 2. Remove permission from all roles
    $permission->roles()->each(function ($role) use ($permission) {
        $role->revokePermissionTo($permission);
    });

    // 3. (Optional) Delete the permission entirely
    $permission->deleteQuietly();

}

function getGlobalImage($type = 'Normal')
{
    // We have favicon Images and other types of images
    if ($type == 'Favicon') {
        return url('storage/assets/img/favicon/favicon.ico');
    }
    if ($type == 'Normal') {
        return url('storage/assets/img/logo.png');
    }

    if ($type == 'Library') {
        return url('storage/assets/img/library_logo.png');
    }
}

function downloadSubManuals($fileName)
{
    if (Storage::disk('privateSubManual')->exists($fileName)) {
        return Storage::disk('privateSubManual')->download($fileName);
    }

    return response()->json(['error' => 'File not found'], 404);
}

function downloadSubManualsContent($fileName)
{
    if (Storage::disk('privateSubManualContent')->exists($fileName)) {
        return Storage::disk('privateSubManualContent')->download($fileName);
    }

    return response()->json(['error' => 'File not found'], 404);
}

function getUser()
{
    return Auth::user();
}

/**
 * PWA-specific file access methods for offline caching
 */
function getPwaSubManualUrl($fileName)
{
    if (Storage::disk('privateSubManual')->exists($fileName)) {
        // For PWA caching, we need a relative URL that the service worker can cache
        // Use proper URL encoding for the filename to handle spaces and special characters
        return '/pwa/download/submanuals/' . rawurlencode($fileName);
    }
    return null;
}

function getPwaSubManualContentUrl($fileName)
{
    if (Storage::disk('privateSubManualContent')->exists($fileName)) {
        // For PWA caching, we need a relative URL that the service worker can cache
        // Use proper URL encoding for the filename
        return '/pwa/download/contents/' . rawurlencode($fileName);
    }
    return null;
}

function downloadPwaSubManuals($fileName)
{
    try {
        // Log the request attempt with detailed debugging
        \Log::info("PWA SubManual request attempt for: {$fileName}");
        \Log::info("Session ID: " . session()->getId());
        \Log::info("Auth check: " . (Auth::check() ? 'YES' : 'NO'));
        \Log::info("Request headers: " . json_encode(request()->headers->all()));
        \Log::info("Request cookies: " . json_encode(request()->cookies->all()));

        $authUser = null;

        // Try session authentication first
        if (Auth::check()) {
            $authUser = Auth::user();
            \Log::info("PWA SubManual authenticated via session: {$authUser->name}");
        } // If session auth fails, try token authentication
        else {
            $token = request()->header('X-PWA-Token') ?? request()->query('pwa_token');
            \Log::info("PWA SubManual token check - Token present: " . (!empty($token) ? 'YES' : 'NO'));
            if ($token) {
                \Log::info("PWA SubManual received token: " . substr($token, 0, 50) . '...');
                $authUser = verifyPwaToken($token);
                if ($authUser) {
                    \Log::info("PWA SubManual authenticated via token: {$authUser->name}");
                } else {
                    \Log::warning("PWA SubManual token verification failed");
                }
            }
        }

        // Check if user is authenticated by either method
        if (!$authUser) {
            \Log::warning("PWA SubManual access denied - not authenticated: {$fileName}");
            \Log::warning("Available session data: " . json_encode(session()->all()));
            return response()->json([
                'error' => 'Authentication required',
                'session_id' => session()->getId(),
                'timestamp' => now()->toISOString(),
                'debug' => [
                    'session_started' => session()->isStarted(),
                    'session_id' => session()->getId(),
                    'has_auth_user' => Auth::check(),
                    'request_method' => request()->method(),
                    'user_agent' => request()->userAgent(),
                    'has_token' => !empty($token)
                ]
            ], 401);
        }

        \Log::info("PWA SubManual request for: {$fileName} by user: {$authUser->name} (ID: {$authUser->id})");

        $disk = Storage::disk('privateSubManual');

        if (!$disk->exists($fileName)) {
            \Log::warning("PWA SubManual file not found: {$fileName}");
            \Log::info("Available files in directory: " . json_encode($disk->files()));
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileSize = $disk->size($fileName);
        $mimeType = $disk->mimeType($fileName);
        $filePath = $disk->path($fileName);

        \Log::info("PWA SubManual file details - Size: {$fileSize}, MIME: {$mimeType}, Path: {$filePath}");

        // Ensure it's a PDF file
        if ($mimeType !== 'application/pdf') {
            \Log::warning("PWA SubManual file is not PDF: {$fileName}, MIME: {$mimeType}");
            return response()->json(['error' => 'File is not a PDF', 'mime_type' => $mimeType], 400);
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
            'X-PWA-Cache' => 'true', // Identifier for PWA requests
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
        ];

        // Add CORS headers for local development
        if (app()->environment('local') || request()->getHost() === '127.0.0.10' || request()->getHost() === 'localhost') {
            $headers['Access-Control-Allow-Origin'] = request()->getSchemeAndHttpHost();
            $headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
            $headers['Access-Control-Allow-Headers'] = 'X-Requested-With, Content-Type, X-PWA-Token, Authorization';
            $headers['Access-Control-Allow-Credentials'] = 'true';
        }

        // Always serve as inline for PWA caching
        $headers['Content-Disposition'] = 'inline; filename="' . basename($fileName) . '"';

        \Log::info("PWA SubManual serving file: {$fileName}");
        return response()->file($filePath, $headers);

    } catch (\Exception $e) {
        \Log::error("PWA SubManual error for {$fileName}: " . $e->getMessage());
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}

function downloadPwaSubManualsContent($fileName)
{
    try {
        // Log the request attempt with detailed debugging
        \Log::info("PWA SubManualContent request attempt for: {$fileName}");
        \Log::info("Session ID: " . session()->getId());
        \Log::info("Auth check: " . (Auth::check() ? 'YES' : 'NO'));
        \Log::info("Request headers: " . json_encode(request()->headers->all()));
        \Log::info("Request cookies: " . json_encode(request()->cookies->all()));

        $authUser = null;

        // Try session authentication first
        if (Auth::check()) {
            $authUser = Auth::user();
            \Log::info("PWA SubManualContent authenticated via session: {$authUser->name}");
        } // If session auth fails, try token authentication
        else {
            $token = request()->header('X-PWA-Token') ?? request()->query('pwa_token');
            \Log::info("PWA SubManualContent token check - Token present: " . (!empty($token) ? 'YES' : 'NO'));
            if ($token) {
                \Log::info("PWA SubManualContent received token: " . substr($token, 0, 50) . '...');
                $authUser = verifyPwaToken($token);
                if ($authUser) {
                    \Log::info("PWA SubManualContent authenticated via token: {$authUser->name}");
                } else {
                    \Log::warning("PWA SubManualContent token verification failed");
                }
            }
        }

        // Check if user is authenticated by either method
        if (!$authUser) {
            \Log::warning("PWA SubManualContent access denied - not authenticated: {$fileName}");
            \Log::warning("Available session data: " . json_encode(session()->all()));
            return response()->json([
                'error' => 'Authentication required',
                'session_id' => session()->getId(),
                'timestamp' => now()->toISOString(),
                'debug' => [
                    'session_started' => session()->isStarted(),
                    'session_id' => session()->getId(),
                    'has_auth_user' => Auth::check(),
                    'request_method' => request()->method(),
                    'user_agent' => request()->userAgent(),
                    'has_token' => !empty($token)
                ]
            ], 401);
        }

        \Log::info("PWA SubManualContent request for: {$fileName} by user: {$authUser->name} (ID: {$authUser->id})");

        $disk = Storage::disk('privateSubManualContent');

        if (!$disk->exists($fileName)) {
            \Log::warning("PWA SubManualContent file not found: {$fileName}");
            \Log::info("Available files in directory: " . json_encode($disk->files()));
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileSize = $disk->size($fileName);
        $mimeType = $disk->mimeType($fileName);
        $filePath = $disk->path($fileName);

        \Log::info("PWA SubManualContent file details - Size: {$fileSize}, MIME: {$mimeType}, Path: {$filePath}");

        // Ensure it's a PDF file
        if ($mimeType !== 'application/pdf') {
            \Log::warning("PWA SubManualContent file is not PDF: {$fileName}, MIME: {$mimeType}");
            return response()->json(['error' => 'File is not a PDF', 'mime_type' => $mimeType], 400);
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
            'X-PWA-Cache' => 'true', // Identifier for PWA requests
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
        ];

        // Add CORS headers for local development
        if (app()->environment('local') || request()->getHost() === '127.0.0.10' || request()->getHost() === 'localhost') {
            $headers['Access-Control-Allow-Origin'] = request()->getSchemeAndHttpHost();
            $headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
            $headers['Access-Control-Allow-Headers'] = 'X-Requested-With, Content-Type, X-PWA-Token, Authorization';
            $headers['Access-Control-Allow-Credentials'] = 'true';
        }

        // Always serve as inline for PWA caching
        $headers['Content-Disposition'] = 'inline; filename="' . basename($fileName) . '"';

        \Log::info("PWA SubManualContent serving file: {$fileName}");
        return response()->file($filePath, $headers);

    } catch (\Exception $e) {
        \Log::error("PWA SubManualContent error for {$fileName}: " . $e->getMessage());
        return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
    }
}

/**
 * Verify PWA authentication token
 */
function verifyPwaToken($token)
{
    try {
        // Split token and signature
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            \Log::warning('Invalid PWA token format');
            return null;
        }

        [$tokenData, $signature] = $parts;

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $tokenData, config('app.key'));
        if (!hash_equals($expectedSignature, $signature)) {
            \Log::warning('Invalid PWA token signature');
            return null;
        }

        // Decode token data
        $data = json_decode(base64_decode($tokenData), true);
        if (!$data) {
            \Log::warning('Invalid PWA token data');
            return null;
        }

        // Check expiration
        if (isset($data['expires_at']) && $data['expires_at'] < time()) {
            \Log::warning('PWA token has expired');
            return null;
        }

        // Get user
        if (isset($data['user_id'])) {
            $user = User::where('uuid', $data['user_id'])->first();
            if ($user) {
                \Log::info('PWA token verified for user: ' . $user->name);
                return $user;
            }
        }

        \Log::warning('PWA token user not found');
        return null;

    } catch (\Exception $e) {
        \Log::error('PWA token verification error: ' . $e->getMessage());
        return null;
    }

    function formatBytes($bytes, int $precision = 2): string
    {
        $bytes = max($bytes, 0);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
