<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



function getGlobalImage($type = 'Normal')
{
    //We have favicon Images and other types of images
    if ($type == 'Favicon') {
        return url('storage/assets/img/favicon/favicon.ico');
    }
    if ($type == 'Normal'){
        return url('storage/assets/img/logo.png');
    }
}


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

