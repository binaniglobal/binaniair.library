<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return view('home');
    }

    function downloadSubManuals($fileName)
    {
        if (Storage::disk('privateSubManual')->exists($fileName)) {
//            return Storage::disk('privateSubManual')->download($fileName);
            $fileContent = Storage::disk('privateSubManual')->get($fileName);
            $mimeType = Storage::disk('privateSubManual')->mimeType($fileName);
//            dd($mimeType);
            return response($fileContent, 200)->header('Content-Type', $mimeType)->header('Content-Disposition', 'inline');
        }
        return response()->json(['error' => 'File not found'], 404);
    }

    function downloadSubManualsContent($fileName)
    {
        if (Storage::disk('privateSubManualContent')->exists($fileName)) {
            $fileContent = Storage::disk('privateSubManualContent')->get($fileName);
            $mimeType = Storage::disk('privateSubManualContent')->mimeType($fileName);
//            dd($mimeType);
            return response($fileContent, 200)->header('Content-Type', $mimeType)->header('Content-Disposition', 'inline');
//            return Storage::disk('privateSubManualContent')->download($fileName);
        }
        return response()->json(['error' => 'File not found'], 404);
    }
}
