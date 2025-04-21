<?php

namespace Database\Seeders;

use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Permission;
use App\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    protected static ?string $password;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(PermissionSeeder::class);
        $this->call(ProductionSeeder::class);

//        $SuperAdmin = User::create([
//            'name' => 'Super-admin',
//            'surname' => 'BinaniAir',
//            'email' => 'super-admin@binaniair.com',
//            'phone' => '09000023456',
//            'password' => static::$password ??= Hash::make('password'),
//            'remember_token' => Str::random(10),
//        ]);
//        $SuperAdmin->assignRole('super-admin');
//        $SuperAdmin->givePermissionTo(['view-home', 'create-manual', 'view-manual', 'edit-manual', 'destroy-manual','create-user', 'view-user', 'edit-user', 'destroy-user', 'reset-password', 'view-report', 'generate-report']);
//
//        $SuperAdmin = User::create([
//            'name' => 'SuperAdmin',
//            'surname' => 'BinaniAir',
//            'email' => 'SuperAdmin@binaniair.com',
//            'phone' => '09000023356',
//            'password' => static::$password ??= Hash::make('password'),
//            'remember_token' => Str::random(10),
//        ]);
//        $SuperAdmin->assignRole('SuperAdmin');
//        $SuperAdmin->givePermissionTo(['view-home','create-manual', 'view-manual', 'edit-manual', 'destroy-manual', 'create-user', 'view-user', 'edit-user', 'destroy-user', 'reset-password']);
//
//        $Admin = User::create([
//            'name' => 'Admin',
//            'surname' => 'BinaniAir',
//            'email' => 'admin@binaniair.com',
//            'phone' => '09200023456',
//            'password' => static::$password ??= Hash::make('password'),
//            'remember_token' => Str::random(10),
//        ]);
//        $Admin->assignRole('admin');
//        $Admin->givePermissionTo(['create-manual', 'view-manual', 'edit-manual', 'create-user', 'view-user', 'edit-user', 'reset-password', 'issue-manual']);
//
//        $user = User::create([
//            'name' => 'User',
//            'surname' => 'BinaniAir',
//            'email' => 'user@binaniair.com',
//            'phone' => '09100023456',
//            'password' => static::$password ??= Hash::make('password'),
//            'remember_token' => Str::random(10),
//        ]);
//        $user->assignRole('user');
//        $user->givePermissionTo(['view-manual']);


//        $users = User::role(['user'])->get();
//        foreach ($users as $user) {
//            // Assign the permission to each user
//            $user->givePermissionTo(['view-manual']);
//        }

    }
}
