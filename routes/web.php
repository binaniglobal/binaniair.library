<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssuingBooksController;
use App\Http\Controllers\ManualItemContentController;
use App\Http\Controllers\ManualsController;
use App\Http\Controllers\ManualsItemController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('index');

Auth::routes([
    'register' => false,
]);

Route::get('/link/storage', function (){
    Artisan::call('storage:link');
});
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}',
    function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/home')->with('success', 'Your email has been verified!');
    })->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/email/verification-notification', function (Request $request) {
    $user = $request->user();
    if (!empty($user)) {
        $user->sendEmailVerificationNotification();
    }
    return back()->with('message', 'Verification link resent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::middleware(['auth','role:super-admin|admin|librarian|user'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    //Start Manuals
    Route::get('/manuals', [ManualsController::class, 'index'])->name('manual.index');
    Route::get('/manual/add', [ManualsController::class, 'show'])->name('manual.add');
    Route::post('/manual/create', [ManualsController::class, 'store'])->name('manual.create');
    Route::post('/manual/edit', [ManualsController::class, 'edit'])->name('manual.edit');
    Route::post('/manual/update', [ManualsController::class, 'update'])->name('manual.update');
    Route::post('/manual/destroy', [ManualsController::class, 'destroy'])->name('manual.destroy');
    //End of View Manuals

    //Start Manual Items
    Route::get('/manual/items/{id}', [ManualsItemController::class, 'index'])->name('manual.items.index');
    Route::get('/manual/items/{id}/add', [ManualsItemController::class, 'create'])->name('manual.items.show');
    Route::post('/manual/items/create', [ManualsItemController::class, 'store'])->name('manual.items.add');
    Route::get('/manual/items/{id}/edit', [ManualsItemController::class, 'edit'])->name('manual.items.edit');
    Route::post('/manual/items/{id}/update/{ids}', [ManualsItemController::class, 'update'])->name('manual.items.update');
    Route::get('/manual/items/{id}/destroy/{ids}', [ManualsItemController::class, 'destroy'])->name('manual.items.destroy');

    //Start Manual Items Contents
    Route::get('/manual/items/content/{id}', [ManualItemContentController::class, 'index'])->name('manual.items.content.index');
    Route::get('/manual/items/content/{id}/add', [ManualItemContentController::class, 'create'])->name('manual.items.content.show');
    Route::post('/manual/items/content/create', [ManualItemContentController::class, 'store'])->name('manual.items.content.add');
    Route::post('/manual/items/content/{id}/edit', [ManualItemContentController::class, 'edit'])->name('manual.items.content.edit');
    Route::post('/manual/items/content/{id}/update', [ManualItemContentController::class, 'update'])->name('manual.items.content.update');
    Route::get('/manual/items/content/{id}/destroy/{ids}', [ManualItemContentController::class, 'destroy'])->name('manual.items.content.destroy');
    //End Manual Items Contents


});


Route::middleware(['auth', 'role:super-admin|admin'])->group(function () {
    //Add Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/usersList', [UserController::class, 'listIndex'])->name('users.index.list');
    Route::get('/users/add', [UserController::class, 'show'])->name('users.add');
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/destroy', [UserController::class, 'destroy'])->name('users.destroy');
    //End Users
});

Route::middleware(['auth', 'role:super-admin|admin|librarian'])->group(function () {
    //Start Issuing Books

    //Route::get('/issue/books', [IssuingBooksController::class, 'index'])->name('issue.books.index');
    //Route::get('/issue/books/add', [IssuingBooksController::class, 'show'])->name('issue.books.show');
    Route::post('/issue/books/create', [IssuingBooksController::class, 'create'])->name('issue.books.create');
    Route::post('/issue/books/update', [IssuingBooksController::class, 'update'])->name('issue.books.update');
    //End Issuing Books

});
