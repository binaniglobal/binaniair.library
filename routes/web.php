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
//Route::get('/test-private-disk', function () {
//    try {
//        $result = Storage::disk('privateSubManual')->put('test.txt', 'This is a test file.');
//        return $result ? 'File written successfully!' : 'Failed to write file.';
//    } catch (\Exception $e) {
//        return $e->getMessage();
//    }
//});

Route::get('/storage/link', function () {
    Artisan::call('storage:link');
});

Route::get('/maintenance/up', function () {
    Artisan::call('up');
});


//Route::middleware(['auth','role:super-admin|admin|librarian|user', 'verified'])->group(function () {
Route::middleware(['auth', 'role:super-admin|SuperAdmin|admin|librarian|user'])->group(function () {
    Route::get('/manual/sub-manuals/file/{filename}', [HomeController::class, 'downloadSubManuals'])->name('download.submanuals');
    Route::get('/manual/sub-manuals/content/file/{filename}', [HomeController::class, 'downloadSubManualsContent'])->name('download.contents');

    Route::get('/profile', [ProfileController::class, 'index'])->middleware('password.confirm')->name('profile');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');

    //Start Manuals
    Route::get('/manuals', [ManualsController::class, 'index'])->middleware(['permission:view-manual'])->name('manual.index');
    Route::get('/manual/add', [ManualsController::class, 'show'])->middleware(['permission:create-manual'])->name('manual.add');
    Route::post('/manual/create', [ManualsController::class, 'store'])->middleware(['permission:create-manual'])->name('manual.create');
    Route::get('/manual/{id}/destroy', [ManualsController::class, 'destroy'])->middleware(['permission:destroy-manual'])->name('manual.destroy');
    //End of View Manuals

    //Start Manual Items
    Route::get('/manual/sub-manuals/{id}', [ManualsItemController::class, 'index'])->middleware(['permission:view-manual'])->name('manual.items.index');
    Route::get('/manual/sub-manuals/{id}/add', [ManualsItemController::class, 'create'])->middleware(['permission:create-manual'])->name('manual.items.show');
    Route::post('/manual/sub-manuals/create', [ManualsItemController::class, 'store'])->middleware(['permission:create-manual'])->name('manual.items.add');
    Route::get('/manual/sub-manuals/{id}/destroy/{ids}', [ManualsItemController::class, 'destroy'])->middleware(['permission:destroy-manual'])->name('manual.items.destroy');

    //Start Manual Items Contents
    Route::get('/manual/sub-manuals/content/{id}', [ManualItemContentController::class, 'index'])->middleware(['permission:view-manual'])->name('manual.items.content.index');
    Route::get('/manual/sub-manuals/content/{id}/add', [ManualItemContentController::class, 'create'])->middleware(['permission:create-manual'])->name('manual.items.content.show');
    Route::post('/manual/sub-manuals/content/create', [ManualItemContentController::class, 'store'])->middleware(['permission:create-manual'])->name('manual.items.content.add');
    Route::get('/manual/sub-manuals/content/{id}/destroy/{ids}', [ManualItemContentController::class, 'destroy'])->middleware(['permission:destroy-manual'])->name('manual.items.content.destroy');
    //End Manual Items Contents
});

Route::middleware(['auth', 'role:super-admin'])->group(function () {

    Route::get('/maintenance/down', function () {
        Artisan::call('down');
    })->middleware(['auth', 'role:super-admin']);

    //Add Roles
    Route::get('/roles', [RolesController::class, 'index'])->name('roles');
    Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RolesController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/update', [RolesController::class, 'update'])->name('roles.update');
    Route::post('/roles/destroy', [RolesController::class, 'destroy'])->name('roles.destroy');
    //End Add Roles

    //Add Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::post('/permissions/destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    //End Add Permissions
});

Route::middleware(['auth', 'role:super-admin|SuperAdmin|admin|librarian'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->middleware(['permission:view-home'])->name('home');

    //Add Users
    Route::get('/users', [UserController::class, 'index'])->middleware(['permission:view-user'])->name('users.index');
    Route::get('/users/add', [UserController::class, 'show'])->middleware(['permission:create-user'])->name('users.add');
    Route::post('/users/create', [UserController::class, 'create'])->middleware(['permission:create-user'])->name('users.create');
    Route::get('/users/d34{id}/{str}s/edit', [UserController::class, 'edit'])->middleware(['permission:edit-user'])->name('users.edit');
    Route::put('/users/f{id}s/update', [UserController::class, 'update'])->middleware(['permission:edit-user'])->name('users.update');
    Route::get('/users/d{id}/{str}a/destroy', [UserController::class, 'destroy'])->middleware(['permission:destroy-user'])->name('users.destroy');
    //End Users

    //Start Issuing Books
//    Route::get('/issue/books', [IssuingBooksController::class, 'index'])->name('issue.books.index');
//    Route::get('/issue/books/add', [IssuingBooksController::class, 'show'])->name('issue.books.show');
//    Route::post('/issue/books/create', [IssuingBooksController::class, 'create'])->name('issue.books.create');
//    Route::post('/issue/books/update', [IssuingBooksController::class, 'update'])->name('issue.books.update');
    //End Issuing Books
});

require __DIR__ . '/mail.php';
require __DIR__ . '/storage.php';
