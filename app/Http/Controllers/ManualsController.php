<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManualsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manuals.index', ['Manuals' => Manuals::all()]);
    }

    public function noOfManuals()
    {
        $count = Manuals::all();
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

        Manuals::create([
            'mid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => $request->manual_name
        ]);
        return redirect()->back()->with('success', 'Folder Created');
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
        $manuals::where('mid', $id)->delete();
        $path = $manualsItem::where('mid', $id)->first();
        if (!empty($path['file_type']) && $path['file_type'] != 'Folder') {
            if (!empty($path['link'])) {
                Storage::delete($path['link']);
                $manualsItem::where('manual_uid', $id)->delete();
                return redirect(route('manual.items.index', $id, $ids))->with('success', 'Folder or File Deleted');
            }
        }else{
            $manualsItem::where('miid', $id)->delete();
            $manualItemContent::where('manual_uid', $id)->delete();
            return redirect(route('manual.items.index', $id, $ids))->with('success', 'Folder');
        }
    }
}
