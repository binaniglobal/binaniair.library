<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\ManualsItem;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return view('manuals.items.contents.index', ['Id' => $id, 'Manual' => ManualsItem::where('miid', $id)->first(), 'Items' => ManualItemContent::where('manual_items_uid', $id)->orderBy('created_at')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ManualItemContent $manualItemContent)
    {
        $request->validate([
            'manual_name' => 'string|unique:manuals_item_contents,name',
            'file' => 'array|max:5', // Limit to 5 uploads
            'file.*' => ['file', 'mimes:pdf', 'max:'.env('FILE_SIZE', 40960)], // Validation rules for each file
        ]);
        $uploadedFiles = $request->file('file');

        if (! $uploadedFiles || ! is_array($uploadedFiles)) {
            return back()->with('error', 'No files were uploaded.');
        }

        foreach ($uploadedFiles as $file) {
            $fileName = $file->getClientOriginalName();
            $fileNameUnique = Str::random(4).'_'.$fileName;
            $path = Storage::disk('privateSubManualContent')->putFileAs('', $file, $fileNameUnique);
            $fileSize = $file->getSize(); // Get file size in bytes
            $mimeType = $file->getClientMimeType(); // Get MIME type

            $customName = Str::beforeLast($fileName, '.pdf');
            $getParentManual = getManualById($request->manual_uid);
            $getItemManual = getManualItemsFolderById($request->manual_uid, $request->id);

            try {

                // Store file data to database
                $fileData = [
                    'manual_uid' => $request->manual_uid,
                    'manual_items_uid' => $request->id,
                    'name' => $customName,
                    'link' => $path,
                    'file_size' => $fileSize, // Size in byte
                    'file_type' => $mimeType, // Application type
                ];
                if (! empty($fileData)) {
                    $manualItemContent::create($fileData);
                    $permissionName = "access-manual-{$getParentManual->name}.{$getItemManual->name}.{$customName}";
                    // Create permission
                    $permission = Permission::firstOrCreate(['name' => $permissionName]);
                    if (auth()->check() && ! auth()->user()->hasPermissionTo($permission)) {
                        auth()->user()->givePermissionTo($permission);
                    }
                } else {
                    Storage::disk('privateSubManualContent')->delete($path);
                }

                return redirect(route('manual.items.content.index', $request->id))->with('success', 'File uploaded Successfully');
            } catch (\Exception $exception) {
                Log::log('error', 'Error Message: '.$exception->getMessage());
                Storage::disk('privateSubManualContent')->delete($path);

                return redirect(route('manual.index'));
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('manuals.items.contents.add', ['Id' => $id, 'Manual' => ManualsItem::where('miid', $id)->first()]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ManualItemContent $manualItemContent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ManualItemContent $manualItemContent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ManualItemContent $manualItemContent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $ids, ManualItemContent $manualItemContent)
    {
        if (Auth::user()->can('destroy-manual')) {
            $path = $manualItemContent::where('micd', $ids)->first();
            if (! empty($path['file_type']) && $path['file_type'] != 'Folder') {
                if (! empty($path['link'])) {
                    if (Storage::disk('privateSubManualContent')->exists($path['link'])) {

                        $getParentManual = getManualById($path['manual_uid']);
                        $getManualItem = getManualItemsFolderById($path['manual_uid'], $path['manual_items_uid']);

                        $permissionName = "access-manual-{$getParentManual->name}.{$getManualItem->name}.{$path['name']}";
                        removePermissionFromAll($permissionName);

                        Storage::disk('privateSubManualContent')->delete($path['link']);
                        $manualItemContent::where('micd', $ids)->delete();

                        return redirect(route('manual.items.content.index', $id, $ids))->with('success', $path['name'].' File Deleted');
                    } else {
                        return response()->json(['error' => 'File not found'], 404);
                    }
                } else {
                    $manualItemContent::where('micd', $ids)->delete();
                    if (env('MAIL_STATUS', 'False') == 'True') {

                    }

                    return redirect(route('manual.items.content.index', $id, $ids))->with('success', $path['name'].' File Deleted');
                }
            }
        } else {
            if (env('MAIL_STATUS', 'False') == 'True') {

            }

            return response()->json(['error' => 'You do not have permission to delete files'], 404);
        }
    }

    /**
     * API endpoint to get manual item content for PWA caching
     */
    public function apiIndex($id)
    {
        $user = auth()->user();
        $manualItem = ManualsItem::where('miid', $id)->first();

        if (! $manualItem) {
            return response()->json([
                'success' => false,
                'message' => 'Manual item not found',
            ], 404);
        }

        $manual = \App\Models\Manuals::where('mid', $manualItem->manual_uid)->first();

        if (! $manual || ! $user->hasPermissionTo("access-manual-{$manual->name}.{$manualItem->name}")) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied',
            ], 403);
        }

        $contents = ManualItemContent::where('manual_items_uid', $id)->orderBy('created_at')->get();

        $contentsData = $contents->map(function ($content) {
            return [
                'id' => $content->micd,
                'manual_items_uid' => $content->manual_items_uid,
                'name' => $content->name,
                'file_path' => $content->link,
                'file_size' => $content->file_size,
                'content_type' => $content->file_type,
                'url' => route('download.contents', $content->link),
                'pwa_url' => getPwaSubManualContentUrl($content->link),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $contentsData->toArray(),
            'manual_item' => [
                'id' => $manualItem->miid,
                'name' => $manualItem->name,
            ],
            'manual' => [
                'id' => $manual->mid,
                'name' => $manual->name,
            ],
            'cached_at' => now()->toISOString(),
        ]);
    }
}
