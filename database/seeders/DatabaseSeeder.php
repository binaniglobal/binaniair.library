<?php

namespace Database\Seeders;

use App\Models\Manuals;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    protected static ?string $password;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superadmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $librarian = Role::create(['name' => 'librarian']);
        $user = Role::create(['name' => 'user']);

        Permission::create(['name' => 'can edit']);
        Permission::create(['name' => 'can delete']);

        Permission::create(['name' => 'issue books']);
        Permission::create(['name' => 'view books']);
        Permission::create(['name' => 'update books']);

        //Works both for Manual and their Items
        Permission::create(['name' => 'add manuals']);
        Permission::create(['name' => 'edit manuals']);
        Permission::create(['name' => 'destroy manuals']);


        $user = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'SuperAdmin',
            'surname' => 'BinaniAir',
            'email' => 'super-admin@example.com',
            'phone' => '09000023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('super-admin');

        $user = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'Admin',
            'surname' => 'BinaniAir',
            'email' => 'admin@example.com',
            'phone' => '09200023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('admin');

        $librarian = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'Librarian',
            'surname' => 'BinaniAir',
            'email' => 'librarian@example.com',
            'phone' => '09130023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $librarian->assignRole('librarian');

        $user = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'User',
            'surname' => 'BinaniAir',
            'email' => 'user@example.com',
            'phone' => '09100023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('user');


        $manual = Manuals::create([
            'mid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'Company Manuals',
        ]);
//        $manual = Manuals::create([
//            'mid' => uuid_create(UUID_TYPE_DEFAULT),
//            'name' => 'NCAA HardCopy',
//            'type' => 1
//        ]);


    }
}
