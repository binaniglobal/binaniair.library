<?php

namespace App\Http\Controllers;

use App\Models\ManualItemContent;
use App\Models\ManualsItem;
use App\Models\Role;
use http\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class ManualItemContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('manuals.items.contents.index', ['Id' => $id, 'Manual' => ManualsItem::where('miid', $id)->orderBy('name', 'asc')->first(), 'Items' => ManualItemContent::where('manual_uid', $id)->orderBy('name', 'asc')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ManualItemContent $manualItemContent)
    {
        $validate = $request->validate([
            'manual_name' => 'string',
            'type' => 'string',
            'file' => 'array|max:5', // Limit to 5 uploads
            'file.*' => 'file|mimes:pdf|max:' . \env('FILE_SIZE'), // Validation rules for each file
        ]);
        $uploadedFiles = $request->file('file');

        if (!$uploadedFiles || !is_array($uploadedFiles)) {
            return back()->with('error', 'No files were uploaded.');
        }

        foreach ($uploadedFiles as $file) {
            $fileName = $file->getClientOriginalName();
            $fileNameUnique = Str::random(4) . '_' . $fileName;
            $path = Storage::disk('privateSubManualContent')->putFileAs('', $file, $fileNameUnique);
            $fileSize = $file->getSize(); // Get file size in bytes
            $mimeType = $file->getClientMimeType(); // Get MIME type

            try {
                // Store file data to database
                $fileData = [
                    'micd' => uuid_create(UUID_TYPE_DEFAULT),
                    'manual_uid' => $request->id,
                    'manual_iid' => $request->manual_uid,
                    'name' => Str::beforeLast($fileName, '.pdf'),
                    'link' => $path,
                    'file_size' => $fileSize, // Size in byte
                    'file_type' => $mimeType, //Application type
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

    private function giveAllPermissions($permission)
    {
        $roles = Role::whereIn('name', ['Admin', 'Librarian'])->get();

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
        if (Auth::user()->can('can destroy')) {
            $path = $manualItemContent::where('micd', $ids)->first();
            if (!empty($path['file_type']) && $path['file_type'] != 'Folder') {
                if (!empty($path['link'])) {
                    if (Storage::disk('privateSubManualContent')->exists($path['link'])) {
                        Storage::disk('privateSubManualContent')->delete($path['link']);
                        $manualItemContent::where('micd', $ids)->delete();
                        return redirect(route('manual.items.content.index', $id, $ids))->with('success', $path['name'] . ' File Deleted');
                    } else {
                        return response()->json(['error' => 'File not found'], 404);
                    }
                } else {
                    $manualItemContent::where('micd', $ids)->delete();
                    if (env('MAIL_STATUS', 'False') == 'True') {

                    }
                    return redirect(route('manual.items.content.index', $id, $ids))->with('success', $path['name'] . ' File Deleted');
                }
            }
        } else {
            if (env('MAIL_STATUS', 'False') == 'True') {

            }
            return response()->json(['error' => 'You do not have permission to delete files'], 404);
        }
    }
}
