<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class ManualsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manuals = Manuals::all();
        return view('manuals.index', ['Manuals' => Manuals::all()]);
    }

    public function noOfManuals()
    {
        $count = ManualsItem::all();
        return $count->count();
    }

    public function getManualName($id)
    {
        $manual = Manuals::where('mid', $id)->first();
        return $manual->name;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'manual_name' => 'string',
        ]);

        $manual = Manuals::create([
            'mid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => $request->manual_name
        ]);
        if ($manual) {
            $permissionName = "access-manual-{$request->manual_name}";
            Permission::Create(['name' => $permissionName]);
            $this->giveAllPermissions($permissionName);
        }
        return redirect()->back()->with('success', 'Folder Created');
    }

    private function giveAllPermissions($permission)
    {
        $roles = Role::whereIn('name', ['SuperAdmin', 'Admin', 'Librarian'])->get();

        foreach ($roles as $role) {
            // Append the permission to all users with the role
            foreach ($role->users as $user) {
                $user->givePermissionTo($permission);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Manuals $manuals)
    {
        return view('manuals.add');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manuals $manuals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manuals $manuals)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Manuals $manuals, ManualsItem $manualsItem, ManualItemContent $manualItemContent)
    {
        $folderManual = $manualsItem::where('miid', $id)->get();
        $folderManualContent = $manualItemContent::where('miid', $id)->get();
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
        $manuals::where('mid', $id)->delete();
        $manualsItem::where('manual_uid', $id)->delete();
        $manualItemContent::where('manual_iid', $id)->delete();
        $permissionNameManual = "access-manual-{$folderManual->name}";
        $permissionNameManualItem = "access-manual-{$folderManualContent->name}";
        $this->removePermissionFromAll($permissionNameManual);
        $this->removePermissionFromAll($permissionNameManualItem);
        return redirect(route('manual.items.index', $id))->with('success', 'Folder Delete');

    }

    private function removePermissionFromAll($permissionName)
    {
        $permission = Permission::findByName($permissionName);
        // Remove the permission globally
        $permission->delete();
    }
}
