<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use Illuminate\Http\Request;
use App\Models\Permission as Permission;

class ManualsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manuals.index', ['Manuals' => Manuals::all()]);
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
        $request->validate([
            'manual_name' => 'string|unique:manuals,name',
        ]);

        $manual = Manuals::create([
            'name' => $request->manual_name
        ]);
        if ($manual) {
            $permissionName = "access-manual-{$request->manual_name}";
            // Create permission
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            if (auth()->check() && !auth()->user()->hasPermissionTo($permission)) {
                auth()->user()->givePermissionTo($permission);
            }
        }
        return redirect(route('manual.index'))->with('success', 'Folder Created');
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
    public function destroy($id)
    {
        deleteManualItemRecursively($id);
        return redirect(route('manual.index', $id))->with('success', 'Manual and its contents are deleted');
    }
}
