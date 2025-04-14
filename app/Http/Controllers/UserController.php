<?php

namespace App\Http\Controllers;

use App\Mail\UserAccountMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'email' => 'required|string|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
            'role' => 'required|string',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id',
        ]);
        $password = Str::random(10);
        $email = Str::before($request->email, '@') . '@binaniair.com';
        //Create Users
        $user = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => $request->first_name,
            'surname' => $request->last_name,
            'email' => $email,
            'phone' => $request->phone,
            'password' => Hash::make($password)
        ]);
        $user->assignRole($validate['role']);
        $permissions = Permission::whereIn('id', $validate['permission'])->pluck('name')->toArray();
        // Assign permissions to the user for the first time
        if ($user->permissions->isEmpty()) {
            $user->syncPermissions($permissions);
        } else {
            return redirect()->back()->withErrors(['error' => 'Permissions have already been assigned to this user. His/Her password is' . $password]);
        }

        if (env('MAIL_STATUS','False') == 'True') {
            $mailData = [
                'subject' => 'Staff Library Login Mail',
                'body' => 'Login details for ' . $validate['first_name'] . ' ' . $validate['last_name'] . '<br/>
            Username/Email Address: ' . $email . '<br/>
            Password: ' . $password . '<br/>
            Lets start using our library system. Cheers
            ',
                'email' => $email,
                'password' => $password,
                'name' => $validate['first_name'] . ' ' . $validate['last_name']
            ];
            Mail::to($email)->send(new UserAccountMail($mailData));
        }
        return redirect(route('users.index'))->with('success', 'User Created Successfully. Please inform the user to check his/her mail for the login details. Password is: ' . $password . '.');
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
        $authUser = Auth::user();
        if ($authUser->hasRole(['super-admin', 'SuperAdmin'])) {
            $roles = Role::whereNot('name', 'super-admin')
                ->where(function ($query) use ($authUser) {
                    $query->whereIn('name', array_merge($authUser->getRoleNames()->toArray(), ['User', 'librarian', 'Admin', 'SuperAdmin']));
                })
                ->get();
        } elseif ($authUser->hasRole(['admin'])) {
            Role::whereNot('name', 'super-admin')
                ->where(function ($query) use ($authUser) {
                    $query->whereIn('name', array_merge($authUser->getRoleNames()->toArray(), ['Admin', 'User', 'librarian']));
                })
                ->get();
        } elseif ($authUser->hasRole(['librarian'])) {
            Role::whereNot('name', 'super-admin')
                ->where(function ($query) use ($authUser) {
                    $query->whereIn('name', array_merge($authUser->getRoleNames()->toArray(), ['User', 'librarian']));
                })
                ->get();
        } else {
            Role::whereNot('name', 'super-admin')
                ->where(function ($query) use ($authUser) {
                    $query->Role::whereIn('name', $authUser->getRoleNames()->toArray())->orWhere('name', 'User');
            })->get();
        }

        $permissions = Permission::where('name', 'like', '%access%')
            ->orWhereIn('name', $authUser->getPermissionNames()->toArray())
            ->get();

        return view('users.add', ['Roles' => $roles, 'Permissions' => $permissions /*Permission::where('name', 'not like', 'can %')->get()*/]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $authUser = Auth::user();
        $Edit = User::where('uid', $id)->first();
        $AssignedPermissions = $Edit->permissions->pluck('id')->toArray();
        $Permissions = Permission::where('name', 'like', '%access%')->orWhereIn('name', $authUser->getPermissionNames())->get();

        return view('users.edit', compact('AssignedPermissions', 'Permissions', 'Edit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::where('uid', $id)->first();

        // Validate the request
        $request->validate([
            'email' => 'required|string',
            'phone' => 'required|numeric',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permission)->pluck('name')->toArray();

        // Update roles and permissions
        $user->syncPermissions($request->permissions ?? [$permissions]);
        return redirect()->route('users.index')->with('success', 'User information updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
//        User::where('uid', $id)->delete();
        $user = User::where('uid', $id)->first();

        //To substitute if the parent boot delete function in the User Model does not work again.
            //To remove the roles and permission of the user to be deleted.
//        $user->syncRoles([]);
//        $user->syncPermissions([]);
//
//        // Delete the user
        $user->delete();
        return redirect()->route('users.index')->with('success', 'The user has been deleted successfully.');
    }
}
