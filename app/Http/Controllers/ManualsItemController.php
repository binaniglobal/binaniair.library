<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ManualsItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('manuals.items.index', ['Id' => $id, 'Manual' => Manuals::where('mid', $id)->first(), 'Items' => ManualsItem::where('manual_uid', $id)->orderBy('created_at')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ManualsItem $manualsItem)
    {
        $validate = Validator::make($request->all(), [
            'manual_name' => 'nullable|string|unique:manuals_items,name', // Allow nullable if not used in 'files' case
            'type' => 'required|string',
            'files' => 'array|max:10',
            'files.*' => [
                'file',
                'mimes:pdf',
                'max:'.env('FILE_SIZE', 40960),
            ],
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $getParentManual = getManualById($request->id);

        if (! empty($request->type) && $request->type !== 'Folder') {
            foreach ($request->file('files') as $file) {

                if (! $file->isValid()) {
                    return response()->json(['error' => 'Uploaded file is not valid'], 400);
                }

                $fileName = $file->getClientOriginalName();
                $customName = Str::beforeLast($fileName, '.pdf');
                $fileNameUnique = Str::random(4).'_'.$fileName;

                $path = Storage::disk('privateSubManual')->putFileAs('', $file, $fileNameUnique);

                $manual = $manualsItem::create([
                    'manual_uid' => $request->id,
                    'name' => $customName,
                    'link' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                ]);

                if ($manual) {
                    $permissionName = "access-manual-{$getParentManual->name}.{$customName}";
                    // Create permission
                    $permission = Permission::firstOrCreate(['name' => $permissionName]);
                    if (auth()->check() && ! auth()->user()->hasPermissionTo($permission)) {
                        auth()->user()->givePermissionTo($permission);
                    }
                } else {
                    Storage::disk('privateSubManual')->delete($path);
                }
            }

            return redirect(route('manual.items.index', $request->id))
                ->with('success', 'Files uploaded successfully!');
        }

        // Else case: creating a Folder
        $permissionName = "access-manual-{$getParentManual->name}.{$request->manual_name}";
        $manual = $manualsItem::create([
            'manual_uid' => $request->id,
            'name' => $request->manual_name,
            'link' => $request->manual_name,
            'file_size' => '0MB',
            'file_type' => 'Folder',
        ]);

        if ($manual) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            if (auth()->check() && ! auth()->user()->hasPermissionTo($permission)) {
                auth()->user()->givePermissionTo($permission);
            }
        }

        return redirect(route('manual.items.index', $request->id))
            ->with('success', 'Folder created successfully!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('manuals.items.add', ['Id' => $id, 'Manual' => Manuals::where('mid', $id)->first()]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ManualsItem $manualsItem) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $ids, Request $request, ManualsItem $manualsItem) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $ids, Request $request, ManualsItem $manualsItem, ManualItemContent $manualItemContent)
    {
        if (Auth::user()->can('can destroy')) {
            $path = $manualsItem::where('manual_uid', $id)->where('miid', $ids)->first();
            $getParentManual = getManualById($id);
            if (! empty($path['file_type']) && $path['file_type'] != 'Folder') {
                if (Storage::disk('privateSubManual')->exists($path['link'])) {
                    Storage::disk('privateSubManual')->delete($path['link']);
                    // Remove permissions of the folders deleted.
                    $getManualItem = getManualItemById($id, $ids);
                    $permissionName = "access-manual-{$getParentManual->name}.{$getManualItem->name}";
                    removePermissionFromAll($permissionName);
                    // Remove the file information from the database.
                    $manualsItem::where('manual_uid', $id)->where('miid', $ids)->where('file_type', 'application/pdf')->delete();

                    return redirect(route('manual.items.index', $id, $ids))->with('success', $path['name'].' File Deleted');
                } else {
                    return response()->json(['error' => 'File not found!'], 404);
                }

            } else {
                $folderManualContent = $manualItemContent::where('manual_items_uid', $ids)->get();
                $getManualItem = getManualItemsFolderById($id, $ids);
                // Remove permissions of the folders deleted.
                $permissionName = "access-manual-{$getParentManual->name}.{$getManualItem->name}";
                removePermissionFromAll($permissionName);

                foreach ($folderManualContent as $item) {
                    if (Storage::disk('privateSubManualContent')->exists($item->link)) {
                        Storage::disk('privateSubManualContent')->delete($item->link);
                    }
                    // Remove all the file permissions
                    $permissionNameAll = "access-manual-{$getParentManual->name}.{$getManualItem->name}.{$item->name}";
                    removePermissionFromAll($permissionNameAll);
                }

                // Delete Folders and its contents
                $manualsItem::where('manual_uid', $id)->where('miid', $ids)->where('file_type', 'Folder')->delete();
                $manualItemContent::where('manual_items_uid', $ids)->delete();

                return redirect(route('manual.items.index', $id, $ids))->with('success', 'Folder Deleted');
            }
        } else {
            return response()->json(['error' => 'You do not have permission to delete.'], 404);
        }

    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $base = log($bytes) / log(1024);
        $suffix = $units[floor($base)];

        return number_format(pow(1024, $base - floor($base)), $precision).' '.$suffix;
    }

    /**
     * API endpoint to get manual items for PWA caching
     */
    public function apiIndex($id)
    {
        $user = auth()->user();
        $manual = Manuals::where('mid', $id)->first();

        if (! $manual || ! $user->hasPermissionTo("access-manual-{$manual->name}")) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied',
            ], 403);
        }

        $items = ManualsItem::where('manual_uid', $id)->orderBy('created_at')->get();

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
                    : getPwaSubManualUrl($item->link),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $itemsData->toArray(),
            'manual' => [
                'id' => $manual->mid,
                'name' => $manual->name,
            ],
            'cached_at' => now()->toISOString(),
        ]);
    }
}
