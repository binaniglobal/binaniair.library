<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

function downloadSubManuals($fileName)
{
    if (Storage::disk('privateSubManual')->exists($fileName)) {
        return Storage::disk('privateSubManual')->download($fileName);
//        return response()->json(['status' => 'success'], 200);
    }
    return response()->json(['error' => 'File not found'], 404);
}

function downloadSubManualsContent($fileName)
{
    if (Storage::disk('privateSubManualContent')->exists($fileName)) {
        return Storage::disk('privateSubManualContent')->download($fileName);
    }
    return response()->json(['error' => 'File not found'], 404);
}

function getUser(){
    return Auth::user();
}

