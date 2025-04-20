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
        //Works both for Manual and their Items
        Permission::create(['name' => 'create-manual']);
        Permission::create(['name' => 'view-manual']);
        Permission::create(['name' => 'edit-manual']);
        Permission::create(['name' => 'destroy-manual']);

        Permission::create(['name' => 'create-user']);
        Permission::create(['name' => 'view-user']);
        Permission::create(['name' => 'edit-user']);
        Permission::create(['name' => 'destroy-user']);

        Permission::create(['name' => 'view-report']);
        Permission::create(['name' => 'generate-report']);

        Permission::create(['name' => 'issue-manual']);
        Permission::create(['name' => 'reset-password']);

        Permission::create(['name' => 'view-home']);

        // Do not forget to add permission like view-manual-company-manuals

        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(['view-home', 'create-manual', 'view-manual', 'edit-manual', 'destroy-manual', 'view-user', 'edit-user', 'destroy-user', 'reset-password', 'view-report', 'generate-report']);

        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(['view-home', 'view-manual']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view-home', 'view-manual']);

        $useRole = Role::create(['name' => 'user']);
        $useRole->givePermissionTo(['view-manual']);

//
        $SuperAdmin = User::create([
            'name' => 'Super-admin',
            'surname' => 'BinaniAir',
            'email' => 'super-admin@binaniair.com',
            'phone' => '09000023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $SuperAdmin->assignRole('super-admin');
        $SuperAdmin->givePermissionTo(['view-home', 'create-manual', 'view-manual', 'edit-manual', 'destroy-manual','create-user', 'view-user', 'edit-user', 'destroy-user', 'reset-password', 'view-report', 'generate-report']);

        $SuperAdmin = User::create([
            'name' => 'SuperAdmin',
            'surname' => 'BinaniAir',
            'email' => 'SuperAdmin@binaniair.com',
            'phone' => '09000023356',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $SuperAdmin->assignRole('SuperAdmin');
        $SuperAdmin->givePermissionTo(['view-home','create-manual', 'view-manual', 'edit-manual', 'destroy-manual', 'create-user', 'view-user', 'edit-user', 'destroy-user', 'reset-password']);

        $Admin = User::create([
            'name' => 'Admin',
            'surname' => 'BinaniAir',
            'email' => 'admin@binaniair.com',
            'phone' => '09200023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $Admin->assignRole('admin');
        $Admin->givePermissionTo(['create-manual', 'view-manual', 'edit-manual', 'create-user', 'view-user', 'edit-user', 'reset-password', 'issue-manual']);

        $user = User::create([
            'name' => 'User',
            'surname' => 'BinaniAir',
            'email' => 'user@binaniair.com',
            'phone' => '09100023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('user');
        $user->givePermissionTo(['view-manual']);


//        $manual = Manuals::create([
//            'name' => 'Company Manuals',
//        ]);
//
//
//        $getParentManual = Manuals::where('name', $manual->name)->first();
//        $permissionName = "access-manual-{$getParentManual->name}";
//        Permission::Create(['name' => $permissionName]);
//        $users = User::role(['super-admin', 'SuperAdmin', 'Admin'])->get();
//        foreach ($users as $user) {
//            // Assign the permission to each user
//            $user->givePermissionTo($permissionName);
//        }


//        $manuals = Manuals::all();
//        foreach ($manuals as $manualId) {
//            $permissionName = "access-manual-{$manualId->name}";
//            Permission::Create(['name' => $permissionName]);
//            $users = User::role(['super-admin', 'SuperAdmin', 'Admin', 'Librarian'])->get();
//
//            foreach ($users as $user) {
//                // Assign the permission to each user
//                $user->givePermissionTo($permissionName);
//            }
//        }


//        $manuals = ManualsItem::all();
//        foreach ($manuals as $manualId) {
//            $getParentManual = Manuals::where('mid', $manualId->manual_uid)->first();;
//            $permissionName = "access-manual-{$getParentManual->name}.{$manualId->name}";
//            Permission::Create(['name' => $permissionName]);
//            $users = User::role(['super-admin', 'SuperAdmin', 'Admin', 'Librarian'])->get();
//            foreach ($users as $user) {
//                // Assign the permission to each user
//                $user->givePermissionTo($permissionName);
//            }
//        }


//      $manual = Manuals::create([
//            'mid' => uuid_create(UUID_TYPE_DEFAULT),
//            'name' => 'NCAA HardCopy',
//            'type' => 1
//        ]);


    }
}
