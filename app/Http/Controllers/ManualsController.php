<?php

namespace App\Http\Controllers;

use App\Models\Manuals;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManualsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $allManuals = Manuals::all();

        // Filter manuals based on permissions and prepare for JS
        $accessibleManuals = $allManuals->filter(function ($manual) use ($user) {
            return $user->hasPermissionTo('access-manual-'.$manual->name);
        })->map(function ($manual) {
            return [
                'id' => $manual->mid,
                'mid' => $manual->mid,
                'name' => $manual->name,
                'type' => $manual->type ?? 0,
            ];
        })->values();

        return view('manuals.index', [
            'Manuals' => $allManuals,
            'AccessibleManuals' => $accessibleManuals,
        ]);
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
            'name' => $request->manual_name,
        ]);
        if ($manual) {
            $permissionName = "access-manual-{$request->manual_name}";
            // Create permission
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            if (auth()->check() && ! auth()->user()->hasPermissionTo($permission)) {
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
        if (Auth::user()->can('destroy-manual')) {
            deleteManualItemRecursively($id);

            return redirect(route('manual.index', $id))->with('success', 'Manual and its contents are deleted');
        }

        return redirect(route('manual.index', $id))->with('success', 'Sorry, this manual could not be deleted');
    }

    /**
     * API endpoint to get manuals data for PWA caching
     */
    public function apiIndex()
    {
        $user = auth()->user();
        $manuals = collect();

        // Get all manuals and filter by permissions
        $allManuals = Manuals::all();

        foreach ($allManuals as $manual) {
            if ($user->hasPermissionTo("access-manual-{$manual->name}")) {
                $manuals->push([
                    'id' => $manual->mid,
                    'name' => $manual->name,
                    'type' => $manual->type,
                    'url' => route('manual.items.index', $manual->mid),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $manuals->toArray(),
            'cached_at' => now()->toISOString(),
        ]);
    }
}
