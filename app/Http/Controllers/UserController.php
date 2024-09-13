<?php

namespace App\Http\Controllers;

use App\Mail\UserAccountMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index', ['Users' => User::withoutRole('super-admin')->get()]);
    }

    public function noOfUsers()
    {
        $count = User::withoutRole('super-admin')->count();
        return $count;
    }

    public function getUserName($id)
    {
        //Get the name of the user
        $user = User::where('uid', $id)->first();
        return $user->name . ' ' . $user->surname;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validate = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'role' => 'required|string'
        ]);
        $password = Str::random(10);
        //Create Users
        $user = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => $request->first_name,
            'surname' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($password)
        ]);
        $user->assignRole($validate['role']);
        $mailData = [
            'subject' => 'Staff Library Login Mail',
            'body' => 'Login details for ' . $validate['first_name'] . ' ' . $validate['last_name'] . '<br/>
            Username/Email Address: ' . $validate['email'] . '<br/>
            Password: ' . $password . '<br/>
            Lets start using our library system. Cheers
            ',
            'email' => $validate['email'],
            'password' => $password,
            'name' => $validate['first_name'] . ' ' . $validate['last_name']
        ];
        Mail::to($request->email)->send(new UserAccountMail($mailData));
        return redirect(route('users.index'))->with('success', 'User Created Successfully. Please inform the user to check his/her mail for the login details.');
    }

    public function validator()
    {

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
    public function show()
    {
        return view('users.add', ['Roles' => Role::where('name', '!=', 'super-admin')->get(), 'Permissions' => Permission::all()]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('users.edit', ['Roles' => User::withoutRole('super-admin')->get(), 'Permissions' => Permission::all(), 'Edit' => User::withoutRole('super-admin')->where('uid', $id)->first()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
