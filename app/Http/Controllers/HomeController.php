<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
     *
     * @return \Illuminate\Contracts\Support\Renderable
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
            $fileContent = Storage::disk('privateSubManual')->get($fileName);
            $mimeType = Storage::disk('privateSubManual')->mimeType($fileName);

            // Get the file size in bytes
            $fileSize = Storage::disk('privateSubManual')->size($fileName);

            // Define the size threshold (100MB in bytes)
            $sizeThreshold = 100 * 1024 * 1024; // 100 MB

            // Determine the Content-Disposition header
            $disposition = ($fileSize > $sizeThreshold) ? 'attachment' : 'inline';

            if ($fileSize > $sizeThreshold) {
                return Storage::disk('privateSubManual')->download($fileName);
            } else {
                return Response::make($fileContent, 200, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => $disposition.'; filename="' . $fileName . '"',
                ]);
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    function downloadSubManualsContent($fileName)
    {
        if (Storage::disk('privateSubManualContent')->exists($fileName)) {
            $fileContent = Storage::disk('privateSubManualContent')->get($fileName);
            $mimeType = Storage::disk('privateSubManualContent')->mimeType($fileName);

            // Get the file size in bytes
            $fileSize = Storage::disk('privateSubManualContent')->size($fileName);

            // Define the size threshold (100MB in bytes)
            $sizeThreshold = 100 * 1024 * 1024; // 100 MB

            // Determine the Content-Disposition header
            $disposition = ($fileSize > $sizeThreshold) ? 'attachment' : 'inline';

            if ($fileSize > $sizeThreshold) {
                return Storage::disk('privateSubManualContent')->download($fileName);
            } else {
                return Response::make($fileContent, 200, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => $disposition.'; filename="' . $fileName . '"',
                ]);
            }
        }
        return response()->json(['error' => 'File not found'], 404);
    }
}
