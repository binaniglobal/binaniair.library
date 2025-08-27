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

class ManualItemContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        // Use findOrFail to automatically handle 404 if the item doesn't exist.
        // Eager load the parent manual relationship to prevent extra queries in the view.
        $manualItem = ManualsItem::with('manual')->findOrFail($id);
        $items = ManualItemContent::where('manual_items_uid', $id)->orderBy('created_at')->get();

        return view('manuals.items.contents.index', [
            'Id' => $id,
            'Manual' => $manualItem,
            'Items' => $items,
        ]);
    }

    /**
     * Store newly created resources in storage.
     */
    public function store(Request $request)
    {
        // --- 1. Improved Validation ---
        // We validate that the parent manual and item exist before proceeding.
        $validated = $request->validate([
            'file' => 'required|array|max:'.env('FILE_SIZE', 40960), // Ensure 'file' is a required array
            'file.*' => ['required', 'file', 'mimes:pdf', 'max:'.env('FILE_SIZE', 40960)],
            'manual_uid' => 'required|string|exists:manuals,mid',
            'id' => 'required|string|exists:manuals_items,miid',
        ]);

        $uploadedFiles = $validated['file'];
        $successCount = 0;
        $errorMessages = [];

        // --- 2. Fetch Parent Data Once ---
        // This is more efficient than querying the database inside the loop.
        $parentManual = Manuals::find($request->manual_uid);
        $itemManual = ManualsItem::find($request->id);

        // --- 3. Use a Database Transaction ---
        // This ensures that if any file fails, all changes for this batch are rolled back,
        // preventing partial uploads and keeping your data consistent.
        DB::beginTransaction();

        try {
            // --- 4. Loop Correctly ---
            // The loop now processes ALL files before any redirect happens.
            foreach ($uploadedFiles as $file) {
                $originalFileName = $file->getClientOriginalName();
                $customName = Str::beforeLast($originalFileName, '.');

                // Check for duplicates within this specific item to avoid conflicts.
                if (ManualItemContent::where('manual_items_uid', $request->id)->where('name', $customName)->exists()) {
                    $errorMessages[] = "A file named '{$customName}' already exists and was skipped.";
                    continue; // Skip this file and move to the next one
                }

                // Use a UUID for the filename to guarantee uniqueness and prevent overwrites.
                $fileNameUnique = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('', $fileNameUnique, 'privateSubManualContent');

                // Store file data in the database
                ManualItemContent::create([
                    'manual_uid' => $request->manual_uid,
                    'manual_items_uid' => $request->id,
                    'name' => $customName,
                    'link' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);

                // Create and assign permission
                $permissionName = "access-manual-{$parentManual->name}.{$itemManual->name}.{$customName}";
                $permission = Permission::firstOrCreate(['name' => $permissionName]);

                if (auth()->check() && !auth()->user()->hasPermissionTo($permission)) {
                    auth()->user()->givePermissionTo($permission);
                }

                $successCount++;
            }

            DB::commit(); // All files processed successfully, commit the transaction.

            // --- 5. Centralized User Feedback ---
            $flashMessage = "{$successCount} file(s) uploaded successfully.";
            if (!empty($errorMessages)) {
                // If there were non-critical errors (like duplicates), attach them to the message.
                $flashMessage .= ' Issues: ' . implode(' ', $errorMessages);
                return redirect()->route('manual.items.content.index', $request->id)->with('warning', $flashMessage);
            }

            return redirect()->route('manual.items.content.index', $request->id)->with('success', $flashMessage);

        } catch (\Exception $exception) {
            DB::rollBack(); // An unexpected error occurred, roll back all database changes.
            Log::error('Error during file upload batch: ' . $exception->getMessage());

            // A cleanup job could be dispatched here to delete any orphaned files
            // that were stored on disk before the database transaction failed.

            return back()->with('error', 'A critical error occurred. No files were saved.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        // Use findOrFail for cleaner code and automatic 404 handling.
        $manualItem = ManualsItem::findOrFail($id);
        return view('manuals.items.contents.add', ['Id' => $id, 'Manual' => $manualItem]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $ids) // Removed unused ManualItemContent injection
    {
        if (!Auth::user()->can('destroy-manual')) {
            return back()->with('error', 'You do not have permission to delete files.');
        }

        // --- 6. Simplified and Safer Deletion ---
        $contentItem = ManualItemContent::find($ids);

        if (!$contentItem) {
            return redirect()->route('manual.items.content.index', $id)->with('error', 'File record not found.');
        }

        // Delete the physical file first
        if ($contentItem->link && Storage::disk('privateSubManualContent')->exists($contentItem->link)) {
            // These helpers should ideally take the model objects directly to be more efficient
            $getParentManual = getManualById($contentItem->manual_uid);
            $getManualItem = getManualItemsFolderById($contentItem->manual_uid, $contentItem->manual_items_uid);

            $permissionName = "access-manual-{$getParentManual->name}.{$getManualItem->name}.{$contentItem->name}";
            removePermissionFromAll($permissionName); // Assuming this helper function exists

            Storage::disk('privateSubManualContent')->delete($contentItem->link);
        }

        $deletedName = $contentItem->name;
        $contentItem->delete();

        // --- 7. Corrected Redirect ---
        // The route 'manual.items.content.index' only expects one parameter ($id).
        return redirect()->route('manual.items.content.index', $id)->with('success', "'{$deletedName}' has been deleted successfully.");
    }

    // Unused resourceful methods can be removed if you don't plan to implement them.
    public function show(ManualItemContent $manualItemContent) {}
    public function edit(ManualItemContent $manualItemContent) {}
    public function update(Request $request, ManualItemContent $manualItemContent) {}
}
