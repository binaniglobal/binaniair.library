<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\ManualsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManualItemContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('manuals.items.contents.index', ['Id' => $id, 'Manual' => ManualsItem::where('miid', $id)->first(), 'Items' => ManualItemContent::where('manual_uid', $id)->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ManualItemContent $manualItemContent)
    {
        $validate = $request->validate([
            'manual_name' => 'string',
            'type' => 'string',
            'files' => 'array|max:5', // Limit to 5 uploads
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,webp,xlsx|max:40960', // Validation rules for each file
        ]);
        if (!empty($validate['type']) && $validate['type'] == 'Folder') {
            $manualItemContent::create([
                'miid' => uuid_create(UUID_TYPE_DEFAULT),
                'manual_uid' => $request->manual_uid,
                'manual_iid' => $request->id,
                'name' => $request->manual_name,
                'file_size' => '0MB', // Size in byte
                'file_type' => 'Folder',
            ]);
            return redirect(route('manual.items.content.index', $request->id))->with('success', 'Folder Created');
        } else {
            foreach ($request->file('file') as $file) {
//                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $fileName = $file->getClientOriginalName();
                $path = $file->storeAs('public/uploads/items/content', $fileName);

                $fileSize = $file->getSize(); // Get file size in bytes
//                $fileSizeInMB = $fileSize / 1024 / 1024; // Convert bytes to MB

                $mimeType = $file->getClientMimeType(); // Get MIME type

                try {
                    // Store file data to database
                    $fileData = [
                        'micd' => uuid_create(UUID_TYPE_DEFAULT),
                        'manual_uid' => $request->id,
                        'manual_iid' => $request->manual_uid,
                        'name' => $fileName,
                        'link' => $path,
                        'file_size' => $fileSize, // Size in byte
                        'file_type' => $mimeType,
                        // Add additional fields (e.g., path)
                    ];
                    // Use your model to save data to the database (replace with your model logic)
                    $manualItemContent::insert($fileData);
                    return redirect(route('manual.items.content.index', $request->id))->with('success', 'File uploaded Successfully');
                } catch (\Exception $exception) {
                    return response()->json([
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'File uploaded successfully!',
        ]);
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
        $path = $manualItemContent::where('micd', $ids)->first();
        if (!empty($path['file_type']) && $path['file_type'] != 'Folder') {
            if (!empty($path['link'])) {
                Storage::delete($path['link']);
                $manualItemContent::where('micd', $ids)->delete();
                return redirect(route('manual.items.content.index', $id, $ids))->with('success', $path['name'].' File Deleted');
            }else{
                $manualItemContent::where('micd', $ids)->delete();
                return redirect(route('manual.items.content.index', $id, $ids))->with('success', $path['name'].' File Deleted');
            }
        }
//        return redirect(route('manual.items.content.index', $id, $ids))->with('success', 'Folder or File Deleted');
    }
}
