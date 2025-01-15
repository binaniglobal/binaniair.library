<?php


use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

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
