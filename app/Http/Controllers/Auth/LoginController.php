<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    //    /**
    //     * Where to redirect users after login.
    //     *
    //     * @var string
    //     */

    //    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return string
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
        // Use the standard Laravel `can()` method for permission checks.
        // The Spatie/laravel-permission package integrates with this automatically.
        if (auth()->user()?->can('view-home')) {
            return '/home';
        }

        // By default, redirect to the manuals page.
        return '/manuals';
    }
}
