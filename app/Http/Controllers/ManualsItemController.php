<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManualsItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        // Use findOrFail for automatic 404 and eager load items for efficiency.
        // This assumes a 'items' relationship exists on the Manuals model.
        $manual = Manuals::with('items')->findOrFail($id);

        return view('manuals.items.index', [
            'Id' => $id,
            'Manual' => $manual,
            'Items' => $manual->items()->orderBy('created_at')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $manual = Manuals::findOrFail($id);
        return view('manuals.items.add', ['Id' => $id, 'Manual' => $manual]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Centralized and conditional validation
        $validated = $request->validate([
            'id' => 'required|string|exists:manuals,mid', // The parent manual ID
            'type' => 'required|string|in:Folder,File',
            'manual_name' => 'required_if:type,Folder|nullable|string|max:255',
            'files' => 'required_if:type,File|nullable|array|max:'.env('FILE_SIZE', 40960),
            'files.*' => 'required_if:type,File|nullable|file|mimes:pdf|max:'.env('FILE_SIZE', 40960),
        ]);

        $parentManual = Manuals::find($validated['id']);

        // 2. Use a database transaction for atomicity
        DB::beginTransaction();
        try {
            if ($validated['type'] === 'Folder') {
                $this->createFolder($validated, $parentManual);
                $message = 'Folder created successfully!';
            } else {
                $this->createFiles($validated, $parentManual);
                $message = 'Files uploaded successfully!';
            }

            DB::commit();
            return redirect()->route('manual.items.index', $parentManual->mid)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create manual item: " . $e->getMessage());
            // Return with the specific error message for user feedback
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $ids)
    {
        if (!Auth::user()->can('destroy-manual')) {
            return back()->with('error', 'You do not have permission to delete.');
        }

        // 4. Simplified and safer fetching
        $itemToDelete = ManualsItem::where('manual_uid', $id)->findOrFail($ids);
        // Eager load the parent manual via relationship (assumes 'manual' relationship exists)
        $parentManual = $itemToDelete->manual;

        DB::beginTransaction();
        try {
            $deletedName = $itemToDelete->name;

            if ($itemToDelete->file_type === 'Folder') {
                $this->deleteFolderAndContents($itemToDelete, $parentManual);
                $message = "Folder '{$deletedName}' and all its contents have been deleted.";
            } else {
                $this->deleteFile($itemToDelete, $parentManual);
                $message = "File '{$deletedName}' has been deleted.";
            }

            DB::commit();
            // 5. Corrected redirect
            return redirect()->route('manual.items.index', $id)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete manual item {$ids}: " . $e->getMessage());
            return back()->with('error', 'An error occurred during deletion.');
        }
    }

    /**
     * API endpoint to get manual items for PWA caching
     */
    public function apiIndex($id)
    {
        $user = auth()->user();
        $manual = Manuals::findOrFail($id);

        if (!$user->hasPermissionTo("access-manual-{$manual->name}")) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        // Use the relationship to get items, which is cleaner and more efficient.
        $items = $manual->items()->orderBy('created_at')->get();

        $itemsData = $items->map(function ($item) {
            return [
                'id' => $item->miid,
                'manual_uid' => $item->manual_uid,
                'name' => $item->name,
                'file_path' => $item->link,
                'file_size' => $item->file_size,
                'file_type' => $item->file_type,
                'url' => $item->file_type === 'Folder'
                    ? route('manual.items.content.index', $item->miid)
                    : route('download.submanuals', $item->link),
                'pwa_url' => $item->file_type === 'Folder'
                    ? null
                    : getPwaSubManualUrl($item->link), // Assuming helper exists
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $itemsData->toArray(),
            'manual' => ['id' => $manual->mid, 'name' => $manual->name],
            'cached_at' => now()->toISOString(),
        ]);
    }

    // --- Private Helper Methods for Cleaner Logic ---

    private function createFolder(array $data, Manuals $parentManual): void
    {
        if (ManualsItem::where('manual_uid', $parentManual->mid)->where('name', $data['manual_name'])->exists()) {
            throw new \Exception("A folder named '{$data['manual_name']}' already exists.");
        }

        $manualItem = ManualsItem::create([
            'manual_uid' => $parentManual->mid,
            'name' => $data['manual_name'],
            'link' => $data['manual_name'],
            'file_size' => 0, // Use integer 0 for consistency
            'file_type' => 'Folder',
        ]);

        $this->assignPermission("access-manual-{$parentManual->name}.{$manualItem->name}");
    }

    private function createFiles(array $data, Manuals $parentManual): void
    {
        foreach ($data['files'] as $file) {
            $customName = Str::beforeLast($file->getClientOriginalName(), '.');

            if (ManualsItem::where('manual_uid', $parentManual->mid)->where('name', $customName)->exists()) {
                throw new \Exception("A file named '{$customName}' already exists. Batch aborted.");
            }

            // 3. Use a better unique name generator
            $fileNameUnique = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('', $fileNameUnique, 'privateSubManual');

            $manualItem = ManualsItem::create([
                'manual_uid' => $parentManual->mid,
                'name' => $customName,
                'link' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
            ]);

            $this->assignPermission("access-manual-{$parentManual->name}.{$manualItem->name}");
        }
    }

    private function deleteFolderAndContents(ManualsItem $folder, Manuals $parentManual): void
    {
        // Assumes 'contents' relationship exists on ManualsItem model
        foreach ($folder->contents as $content) {
            removePermissionFromAll("access-manual-{$parentManual->name}.{$folder->name}.{$content->name}");
            if ($content->link && Storage::disk('privateSubManualContent')->exists($content->link)) {
                Storage::disk('privateSubManualContent')->delete($content->link);
            }
        }
        $folder->contents()->delete(); // Bulk delete child records from DB

        removePermissionFromAll("access-manual-{$parentManual->name}.{$folder->name}");
        $folder->delete();
    }

    private function deleteFile(ManualsItem $fileItem, Manuals $parentManual): void
    {
        if ($fileItem->link && Storage::disk('privateSubManual')->exists($fileItem->link)) {
            Storage::disk('privateSubManual')->delete($fileItem->link);
        }
        removePermissionFromAll("access-manual-{$parentManual->name}.{$fileItem->name}");
        $fileItem->delete();
    }

    private function assignPermission(string $permissionName): void
    {
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        if (auth()->check() && !auth()->user()->hasPermissionTo($permission)) {
            auth()->user()->givePermissionTo($permission);
        }
    }

    // Unused resourceful methods can be removed if not needed.
    public function show(ManualsItem $manualsItem) {}
    public function edit($id) {}
    public function update($id, $ids, Request $request, ManualsItem $manualsItem) {}


    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $base = log($bytes) / log(1024);
        $suffix = $units[floor($base)];

        return number_format(pow(1024, $base - floor($base)), $precision).' '.$suffix;
    }

}
