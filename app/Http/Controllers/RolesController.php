<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Role $role)
    {
        return view('settings.roles.index', ['Role' => $role::where('name', 'not like','super-admin')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Role $role)
    {
        return view('settings.roles.add', ['Role' => $role]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles|max:10',
        ]);
        Role::create(['name' => $validatedData['name'], 'guard_name'=>'web']);
        if(env('MAIL_STATUS','False') == 'True') {

        }
        return redirect()->route('roles');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
