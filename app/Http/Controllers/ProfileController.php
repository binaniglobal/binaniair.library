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
        $user = User::find(Auth::user()->id);
        $validate = $request->validate([
            'phone' => 'numeric|digits_between:10,15',
            'currentPassword' => 'string',
            'newPassword' => 'string|min:8|regex:/[a-z]/|regex:/[\d\s\W]/',
            'confirmPassword' => 'string|min:8|same:newPassword',
        ]);

        if (!empty($validate['phone'])) {
            $user->phone = $validate['phone'];
        }

        if (!empty($validate['currentPassword'])) {
            if (Hash::make((string)$validate['currentPassword']) === (string)$user->password) {
                if ($validate['newPassword'] === $validate['confirmPassword']) {
                    $user->password = Hash::make($validate['newPassword']);
                }
            }
        }

        $user->save();
        return redirect('/profile')->with('success', 'Profile updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
