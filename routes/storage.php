<?php


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/link/storage', function (){
    Artisan::call('storage:link');
});
