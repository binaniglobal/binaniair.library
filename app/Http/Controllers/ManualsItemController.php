<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class ManualsItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('manuals.items.index', ['Id' => $id, 'Manual' => Manuals::where('mid', $id)->orderBy('name', 'asc')->first(), 'Items' => ManualsItem::where('manual_uid', $id)->orderBy('name', 'asc')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ManualsItem $manualsItem)
    {
        $validate = $request->validate([
            'manual_name' => 'string',
            'type' => 'string',
            'files' => 'array|max:10', // Limit to 5 uploads
            'files.*' => 'file|mimes:pdf|max:' . \env('FILE_SIZE'), // Validation rules for each file
        ]);
        if (!empty($validate['type']) && $validate['type'] != 'Folder') {
            foreach ($request->file('files') as $key => $file) {
                // Check if file is valid
                if (!$file->isValid()) {
                    return response()->json(['error' => 'Uploaded file is not valid'], 400);
                }
                //Please make sure there is no . It should only exist when there is a format after it.
                $fileName = $file->getClientOriginalName();
                $fileNameUnique = Str::random(4) . '_' . $fileName;
                $path = Storage::disk('privateSubManual')->putFileAs('', $file, $fileNameUnique);

                $manuals = $manualsItem::create([
                    'miid' => uuid_create(UUID_TYPE_DEFAULT),
                    'manual_uid' => $request->id,
                    'name' => Str::beforeLast($fileName, '.pdf'),
                    'link' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                ]);
                $getParentManual = $this->getManualById($request->id);
                if ($manuals) {
                    $permissionName = "access-manual-{$getParentManual->name}.{$request->manual_name}";
                    Permission::Create(['name' => $permissionName]);
                }

                return redirect(route('manual.items.index', $request->id))->with(['success', 'Files uploaded successfully!']);
            }
        } else {
            $manual = ManualsItem::create([
                'miid' => uuid_create(UUID_TYPE_DEFAULT),
                'manual_uid' => $request->id,
                'name' => $request->manual_name,
                'link' => $request->manual_name,
                'file_size' => '0MB', // Size in byte
                'file_type' => 'Folder',
            ]);
            $getParentManual = $this->getManualById($request->id);
            if ($manual) {
                $permissionName = "access-manual-{$getParentManual->name}.{$request->manual_name}";
                Permission::Create(['name' => $permissionName]);
                $users = User::role(['SuperAdmin', 'Admin', 'Librarian'])->get();

                foreach ($users as $user) {
                    // Assign the permission to each user
                    $user->givePermissionTo($permissionName);
                }
            }
            return redirect(route('manual.items.index', $request->id))->with('success', 'Folder Created');
        }
    }

    private function getManualById($id)
    {
        return Manuals::where('mid', $id)->first();
    }

    private function getManualItemsById($id)
    {
        return ManualsItem::where('manual_uid', $id)->where('file_type', 'Folder')->get();
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
    public function show(ManualsItem $manualsItem)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $ids, Request $request, ManualsItem $manualsItem)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public
    function destroy($id, $ids, Request $request, ManualsItem $manualsItem, ManualItemContent $manualItemContent)
    {
        if (Auth::user()->can('can destroy')) {
            $path = $manualsItem::where('manual_uid', $id)->where('miid', $ids)->first();
            if (!empty($path['file_type']) && $path['file_type'] != 'Folder') {
                if (Storage::disk('privateSubManual')->exists($path['link'])) {
                    Storage::disk('privateSubManual')->delete($path['link']);
                    $manualsItem::where('manual_uid', $id)->where('miid', $ids)->where('file_type', 'application/pdf')->delete();
                    return redirect(route('manual.items.index', $id, $ids))->with('success', $path['name'] . ' File Deleted');
                } else {
                    return response()->json(['error' => 'File not found!'], 404);
                }

            } else {
                $folderManual = $manualsItem::where('manual_uid', $id)->where('file_type', 'Folder')->get();
                $folderManualContent = $manualItemContent::where('manual_iid', $id)->get();
                foreach ($folderManual as $item) {
                    if (Storage::disk('privateSubManual')->exists($item->link)) {
                        Storage::disk('privateSubManual')->delete($item->link);
                    }
                }
                foreach ($folderManualContent as $item) {
                    if (Storage::disk('privateSubManualContent')->exists($item->link)) {
                        Storage::disk('privateSubManualContent')->delete($item->link);
                    }
                }
                $getParentManual = $this->getManualById($request->id);
                $getManualItem = $this->getManualItemsById($request->id);
                foreach ($getManualItem as $item) {
                    $permissionName = "access-manual-{$getParentManual->name}.{$item->name}";
                    $this->removePermissionFromAll($permissionName);
                }
                $manualsItem::where('manual_uid', $id)->where('file_type', 'Folder')->delete();
                $manualItemContent::where('manual_iid', $id)->delete();
                return redirect(route('manual.items.index', $id, $ids))->with('success', 'Folder Deleted');
            }
        } else {
            return response()->json(['error' => 'You do not have permission to delete.'], 404);
        }

    }

    private function removePermissionFromAll($permissionName)
    {
        $permission = Permission::findByName($permissionName);
        // Remove the permission globally
        $permission->delete();
    }

    public
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $base = log($bytes) / log(1024);
        $suffix = $units[floor($base)];

        return number_format(pow(1024, $base - floor($base)), $precision) . ' ' . $suffix;
    }
}
