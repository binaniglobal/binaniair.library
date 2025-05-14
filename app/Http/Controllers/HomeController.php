<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        if (Auth::user()->hasPermissionTo('view-home')) {
            return view('home');
        } else {
            return view('manuals.index');
        }
    }

    function downloadSubManuals($fileName)
    {
        if (Storage::disk('privateSubManual')->exists($fileName)) {
            $fileSize = Storage::disk('privateSubManual')->size($fileName);
            $fileContent = Storage::disk('privateSubManual')->get($fileName);
            $mimeType = Storage::disk('privateSubManual')->mimeType($fileName);

            if ($fileSize > 104857600) { // 100MB in bytes
                return response($fileContent, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            } else {

                return response($fileContent, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'inline');
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    function downloadSubManualsContent($fileName)
    {
        if (Storage::disk('privateSubManualContent')->exists($fileName)) {
            $fileSize = Storage::disk('privateSubManualContent')->size($fileName);
            $fileContent = Storage::disk('privateSubManualContent')->get($fileName);
            $mimeType = Storage::disk('privateSubManualContent')->mimeType($fileName);
            if ($fileSize > 104857600) { // 100MB in bytes
                return response($fileContent, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            } else {
                return response($fileContent, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'inline');
            }
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
