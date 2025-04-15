<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssuingBooksController;
use App\Http\Controllers\ManualItemContentController;
use App\Http\Controllers\ManualsController;
use App\Http\Controllers\ManualsItemController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('auth.login');
})->name('index');

//Route::get('/hash', function () {
//    return view('welcome');
//});

Auth::routes([
    'register' => false,
]);


Route::get('/test-private-disk', function () {
    try {
        $result = Storage::disk('privateSubManual')->put('test.txt', 'This is a test file.');
        return $result ? 'File written successfully!' : 'Failed to write file.';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});


Route::middleware(['auth','role:super-admin|admin|librarian|user', 'verified'])->group(function () {
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/manual/sub-manuals/file/{filename}', [HomeController::class, 'downloadSubManuals'])->name('download.submanuals');
    Route::get('/manual/sub-manuals/content/file/{filename}', [HomeController::class, 'downloadSubManualsContent'])->name('download.contents');

    Route::get('/profile', [ProfileController::class, 'index'])->middleware('password.confirm')->name('profile');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');

    //Start Manuals
    Route::get('/manuals', [ManualsController::class, 'index'])->name('manual.index');

    Route::get('/manuals2', function (){
        return 'Hello World';
    })->name('manual.index2');

    Route::get('/manual/add', [ManualsController::class, 'show'])->name('manual.add');
    Route::post('/manual/create', [ManualsController::class, 'store'])->name('manual.create');
    Route::post('/manual/destroy', [ManualsController::class, 'destroy'])->name('manual.destroy');
    //End of View Manuals

    //Start Manual Items
    Route::get('/manual/sub-manuals/{id}', [ManualsItemController::class, 'index'])->name('manual.items.index');
    Route::get('/manual/sub-manuals/{id}/add', [ManualsItemController::class, 'create'])->name('manual.items.show');
    Route::post('/manual/sub-manuals/create', [ManualsItemController::class, 'store'])->name('manual.items.add');
    Route::get('/manual/sub-manuals/{id}/destroy/{ids}', [ManualsItemController::class, 'destroy'])->name('manual.items.destroy');

    //Start Manual Items Contents
    Route::get('/manual/sub-manuals/content/{id}', [ManualItemContentController::class, 'index'])->name('manual.items.content.index');
    Route::get('/manual/sub-manuals/content/{id}/add', [ManualItemContentController::class, 'create'])->name('manual.items.content.show');
    Route::post('/manual/sub-manuals/content/create', [ManualItemContentController::class, 'store'])->name('manual.items.content.add');
    Route::get('/manual/sub-manuals/content/{id}/destroy/{ids}', [ManualItemContentController::class, 'destroy'])->name('manual.items.content.destroy');
    //End Manual Items Contents

});

Route::middleware(['auth', 'role:super-admin|admin'])->group(function () {

    //Add Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::post('/permissions/destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    //End Add Permissions

});

Route::middleware(['auth', 'role:super-admin'])->group(function () {
    //Add Roles
    Route::get('/roles', [RolesController::class, 'index'])->name('roles');
    Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RolesController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/update', [RolesController::class, 'update'])->name('roles.update');
    Route::post('/roles/destroy', [RolesController::class, 'destroy'])->name('roles.destroy');
    //End Add Roles
});

Route::middleware(['auth', 'role:super-admin|admin|librarian'])->group(function () {
    //Add Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/usersList', [UserController::class, 'listIndex'])->name('users.index.list');
    Route::get('/users/add', [UserController::class, 'show'])->name('users.add');
    Route::get('/users/d34{id}/{str}s/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::put('/users/f{id}s/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/d{id}/{str}a/destroy', [UserController::class, 'destroy'])->name('users.destroy');
    //End Users


    //Start Issuing Books
    //Route::get('/issue/books', [IssuingBooksController::class, 'index'])->name('issue.books.index');
    //Route::get('/issue/books/add', [IssuingBooksController::class, 'show'])->name('issue.books.show');
//    Route::post('/issue/books/create', [IssuingBooksController::class, 'create'])->name('issue.books.create');
//    Route::post('/issue/books/update', [IssuingBooksController::class, 'update'])->name('issue.books.update');
    //End Issuing Books
});

require __DIR__.'/mail.php';
require __DIR__.'/storage.php';
