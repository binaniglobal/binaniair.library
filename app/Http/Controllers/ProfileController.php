<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('profile.index', ['User' => Auth::user()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $validate = $request->validate([
            'phone' => 'numeric|digits_between:10,15',
            'current_password' => 'string|min:8|current_password',
            'password' => 'string|min:8|confirmed',
            'password_confirmation' => 'string|min:8|same:password',
        ]);
        $profile = '';

        if (!empty($validate['phone'])) {
            $user->phone = $validate['phone'];
            $user->save();
            $profile = 'Phone-number Updated';
        }
        if (!empty($validate['current_password'])) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            $profile = 'Password Updated';
        }

        return redirect('/profile')->with('success', $profile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
