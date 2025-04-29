<?php

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Permission as Permission;
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
    //We have favicon Images and other types of images
    if ($type == 'Favicon') {
        return url('storage/assets/img/favicon/favicon.ico');
    }
    if ($type == 'Normal') {
        return url('storage/assets/img/logo.png');
    }

    if ($type == 'Library'){
        return url('storage/assets/img/library_logo.png');
    }
}


function downloadSubManuals($fileName)
{
    if (Storage::disk('privateSubManual')->exists($fileName)) {
        return Storage::disk('privateSubManual')->download($fileName);
//        return response()->json(['status' => 'success'], 200);
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

