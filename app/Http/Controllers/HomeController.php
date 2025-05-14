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
        $disk = Storage::disk('privateSubManual'); // Get disk instance once

        if ($disk->exists($fileName)) {
            $fileSize = $disk->size($fileName);
            $mimeType = $disk->mimeType($fileName);
            $filePath = $disk->path($fileName); // Get the absolute path to the file

            $headers = [
                'Content-Type' => $mimeType,
            ];

            if ($fileSize >= 104857600) { // 100MB in bytes
                // Use response()->download() for forced download with a filename
                return response()->download($filePath, $fileName, $headers);
            } else {
                // Use response()->file() for inline display
                // We need to manually set Content-Disposition to inline if using response()->file()
                $headers['Content-Disposition'] = 'inline';
                return response()->file($filePath, $headers);
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    function downloadSubManualsContent($fileName)
    {

        $disk = Storage::disk('privateSubManualContent'); // Get disk instance once

        if ($disk->exists($fileName)) {
            $fileSize = $disk->size($fileName);
            $mimeType = $disk->mimeType($fileName);
            $filePath = $disk->path($fileName); // Get the absolute path to the file

            $headers = [
                'Content-Type' => $mimeType,
            ];

            if ($fileSize >= 104857600) { // 100MB in bytes
                // Use response()->download() for forced download with a filename
                return response()->download($filePath, $fileName, $headers);
            } else {
                // Use response()->file() for inline display
                // We need to manually set Content-Disposition to inline if using response()->file()
                $headers['Content-Disposition'] = 'inline';
                return response()->file($filePath, $headers);
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }
}
