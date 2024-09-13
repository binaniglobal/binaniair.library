<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManualsItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('manuals.items.index', ['Id' => $id, 'Manual' => Manuals::where('mid', $id)->first(), 'Items' => ManualsItem::where('manual_uid', $id)->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ManualsItem $manualsItem)
    {
        $validate = $request->validate([
            'manual_name' => 'string',
            'type' => 'string',
            'files' => 'array|max:5', // Limit to 5 uploads
            'files.*' => 'file|mimes:jpg,png,jpeg,pdf,doc,docx,webp,xlsx|max:40960', // Validation rules for each file
        ]);
        if (!empty($validate['type']) && $validate['type'] != 'Folder') {
            foreach ($request->file('files') as $key => $file) {

//                $file->storeAs('public/storage/uploads', $file->getClientOriginalName());
//                dd($request->all());
                $fileName = $file->getClientOriginalName();
                $manualsItem->miid = uuid_create(UUID_TYPE_DEFAULT);
                $manualsItem->manual_uid = $request->id;
                $manualsItem->name = $fileName;
                $manualsItem->link = $file->storeAs('public/uploads/items', $fileName);
                $manualsItem->file_size = $file->getSize();;
                $manualsItem->file_type = $file->getClientMimeType();
                $manualsItem->save();

                return redirect(route('manual.items.index', $request->id))->with('success', 'Files uploaded successfully!');
            }
        } else {
            ManualsItem::create([
                'miid' => uuid_create(UUID_TYPE_DEFAULT),
                'manual_uid' => $request->id,
                'name' => $request->manual_name,
                'link' => $request->manual_name,
                'file_size' => '0MB', // Size in byte
                'file_type' => 'Folder',
            ]);
            return redirect(route('manual.items.index', $request->id))->with('success', 'Folder Created');
        }
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
        return view('manuals.items.edit', ['Id' => $id, 'Manual' => ManualsItem::where('miid', $id)->first()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $ids, Request $request, ManualsItem $manualsItem)
    {
        $manualsItem::where('miid', $id)->update(['name' => $request->manual_name, 'link' => $request->manual_name]);
        return redirect(route('manual.items.index', $ids, $id))->with('success', 'File Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $ids, Request $request, ManualsItem $manualsItem, ManualItemContent $manualItemContent)
    {
        $path = $manualsItem::where('miid', $ids)->first();
        if (!empty($path['file_type']) && $path['file_type'] != 'Folder') {
            if (!empty($path['link'])) {
                Storage::delete($path['link']);
                $manualsItem::where('miid', $ids)->delete();
                return redirect(route('manual.items.index', $id, $ids))->with('success', $path['name'].' File Deleted');
            }
        }else{
            $manualsItem::where('miid', $ids)->delete();
            $manualItemContent::where('manual_uid', $ids)->delete();
            return redirect(route('manual.items.index', $id, $ids))->with('success', 'Folder');
        }

    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $base = log($bytes) / log(1024);
        $suffix = $units[floor($base)];

        return number_format(pow(1024, $base - floor($base)), $precision) . ' ' . $suffix;
    }
}
