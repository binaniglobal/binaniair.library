<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssuingBooksController;
use App\Http\Controllers\ManualItemContentController;
use App\Http\Controllers\ManualsController;
use App\Http\Controllers\ManualsItemController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('auth.login');
})->name('index');

Auth::routes([
    'register' => false,
]);

Route::get('/storage/link', function () {
    Artisan::call('storage:link');
});

Route::get('/maintenance/up', function () {
    Artisan::call('up');
});

// Route::middleware(['auth','role:super-admin|admin|librarian|user', 'verified'])->group(function () {
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/manual/sub-manuals/file/{filename}', [HomeController::class, 'downloadSubManuals'])->name('download.submanuals');
    Route::get('/manual/sub-manuals/content/file/{filename}', [HomeController::class, 'downloadSubManualsContent'])->name('download.contents');

    // PWA API endpoints
    Route::get('/api/manuals', [ManualsController::class, 'apiIndex'])->name('api.manuals');
    Route::get('/api/manual/{id}/items', [ManualsItemController::class, 'apiIndex'])->name('api.manual.items');
    Route::get('/api/manual-item/{id}/content', [ManualItemContentController::class, 'apiIndex'])->name('api.manual.content');

    // PWA Status page
    Route::get('/pwa-status', function () {
        return view('pwa-status');
    })->name('pwa.status');

    // PWA Authentication token endpoint
    Route::get('/pwa/auth-token', function () {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        // Generate a simple token based on session and user info
        $tokenData = [
            'user_id' => $user->uuid,
            'session_id' => session()->getId(),
            'expires_at' => now()->addHours(24)->timestamp
        ];

        // Create a signed token
        $token = base64_encode(json_encode($tokenData));
        $signature = hash_hmac('sha256', $token, config('app.key'));

        return response()->json([
            'token' => $token . '.' . $signature,
            'expires_at' => $tokenData['expires_at'],
            'user' => [
                'id' => $user->uuid,
                'name' => $user->name
            ]
        ]);
    })->name('pwa.auth.token');

//    // Debug route to test URL encoding
//    Route::get('/debug-pwa-url/{filename}', function ($filename) {
//        return response()->json([
//            'original_filename' => $filename,
//            'url_decoded' => urldecode($filename),
//            'url_encoded' => urlencode($filename),
//            'pwa_submanual_url' => getPwaSubManualUrl($filename),
//            'file_exists' => Storage::disk('privateSubManual')->exists($filename),
//            'file_exists_decoded' => Storage::disk('privateSubManual')->exists(urldecode($filename)),
//        ]);
//    })->name('debug.pwa.url');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');

    // Start Manuals
    Route::get('/manuals', [ManualsController::class, 'index'])->middleware(['permission:view-manual'])->name('manual.index');
    Route::get('/manual/add', [ManualsController::class, 'show'])->middleware(['permission:create-manual'])->name('manual.add');
    Route::post('/manual/create', [ManualsController::class, 'store'])->middleware(['permission:create-manual'])->name('manual.create');
    Route::get('/manual/{id}/destroy', [ManualsController::class, 'destroy'])->middleware(['permission:destroy-manual'])->name('manual.destroy');
    // End of View Manuals

    // Start Manual Items
    Route::get('/manual/sub-manuals/{id}', [ManualsItemController::class, 'index'])->middleware(['permission:view-manual'])->name('manual.items.index');
    Route::get('/manual/sub-manuals/{id}/add', [ManualsItemController::class, 'create'])->middleware(['permission:create-manual'])->name('manual.items.show');
    Route::post('/manual/sub-manuals/create', [ManualsItemController::class, 'store'])->middleware(['permission:create-manual'])->name('manual.items.store');
    Route::get('/manual/sub-manuals/{id}/destroy/{ids}', [ManualsItemController::class, 'destroy'])->middleware(['permission:destroy-manual'])->name('manual.items.destroy');

    // Start Manual Items Contents
    Route::get('/manual/sub-manuals/content/{id}', [ManualItemContentController::class, 'index'])->middleware(['permission:view-manual'])->name('manual.items.content.index');
    Route::get('/manual/sub-manuals/content/{id}/add', [ManualItemContentController::class, 'create'])->middleware(['permission:create-manual'])->name('manual.items.content.show');
    Route::post('/manual/sub-manuals/content/create', [ManualItemContentController::class, 'store'])->middleware(['permission:create-manual'])->name('manual.items.content.store');
    Route::get('/manual/sub-manuals/content/{id}/destroy/{ids}', [ManualItemContentController::class, 'destroy'])->middleware(['permission:destroy-manual'])->name('manual.items.content.destroy');
    // End Manual Items Contents
});

Route::middleware(['auth', 'role:super-admin'])->group(function () {

    Route::get('/maintenance/down', function () {
        Artisan::call('down');
    })->middleware(['auth', 'role:super-admin']);

    // Add Roles
    Route::get('/roles', [RolesController::class, 'index'])->name('roles');
    Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RolesController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/update', [RolesController::class, 'update'])->name('roles.update');
    Route::post('/roles/destroy', [RolesController::class, 'destroy'])->name('roles.destroy');
    // End Add Roles

    // Add Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::post('/permissions/destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    // End Add Permissions
});

Route::middleware(['auth', 'role:super-admin|SuperAdmin|admin|librarian', 'redirect'])->group(function () {
    Route::middleware(['auth', 'role:super-admin|SuperAdmin|admin|librarian'])->group(function () {
        // Add Users
        Route::get('/users', [UserController::class, 'index'])->middleware(['permission:view-user'])->name('users.index');
        Route::get('/users/add', [UserController::class, 'show'])->middleware(['permission:create-user'])->name('users.add');
        Route::post('/users/create', [UserController::class, 'create'])->middleware(['permission:create-user'])->name('users.create');
        Route::get('/users/d34{id}/{str}s/edit', [UserController::class, 'edit'])->middleware(['permission:edit-user'])->name('users.edit');
        Route::put('/users/f{id}s/update', [UserController::class, 'update'])->middleware(['permission:edit-user'])->name('users.update');
        Route::get('/users/d{id}/{str}a/destroy', [UserController::class, 'destroy'])->middleware(['permission:destroy-user'])->name('users.destroy');
        // End Users

        // Start Issuing Books
        //    Route::get('/issue/books', [IssuingBooksController::class, 'index'])->name('issue.books.index');
        //    Route::get('/issue/books/add', [IssuingBooksController::class, 'show'])->name('issue.books.show');
        //    Route::post('/issue/books/create', [IssuingBooksController::class, 'create'])->name('issue.books.create');
        //    Route::post('/issue/books/update', [IssuingBooksController::class, 'update'])->name('issue.books.update');
        // End Issuing Books
    });
});

// PWA-specific download routes for offline caching
// These routes use session middleware to ensure proper authentication
Route::middleware(['web'])->group(function () {
    // Handle CORS preflight requests for local development
//    Route::options('/pwa/download/submanuals/{filename}', function ($filename) {
//        $headers = [];
//        if (app()->environment('local') || request()->getHost() === '127.0.0.10' || request()->getHost() === 'localhost') {
//            $headers = [
//                'Access-Control-Allow-Origin' => request()->getSchemeAndHttpHost(),
//                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
//                'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, X-PWA-Token, Authorization',
//                'Access-Control-Allow-Credentials' => 'true',
//                'Access-Control-Max-Age' => '86400',
//            ];
//        }
//        return response('', 200, $headers);
//    })->where('filename', '.*');
//
//    Route::options('/pwa/download/contents/{filename}', function ($filename) {
//        $headers = [];
//        if (app()->environment('local') || request()->getHost() === '127.0.0.10' || request()->getHost() === 'localhost') {
//            $headers = [
//                'Access-Control-Allow-Origin' => request()->getSchemeAndHttpHost(),
//                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
//                'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, X-PWA-Token, Authorization',
//                'Access-Control-Allow-Credentials' => 'true',
//                'Access-Control-Max-Age' => '86400',
//            ];
//        }
//        return response('', 200, $headers);
//    })->where('filename', '.*');

    Route::get('/pwa/download/submanuals/{filename}', function ($filename) {
        // Decode the filename since it may be URL-encoded
        $decodedFilename = urldecode($filename);
        return downloadPwaSubManuals($decodedFilename);
    })->name('pwa.download.submanuals')->where('filename', '.*');

    Route::get('/pwa/download/contents/{filename}', function ($filename) {
        // Decode the filename since it may be URL-encoded
        $decodedFilename = urldecode($filename);
        return downloadPwaSubManualsContent($decodedFilename);
    })->name('pwa.download.contents')->where('filename', '.*');
});

require __DIR__.'/mail.php';
require __DIR__.'/storage.php';
