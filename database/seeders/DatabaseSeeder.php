<?php

namespace Database\Seeders;

use App\Models\Manuals;
use App\Models\ManualsItem;
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
        //Works both for Manual and their Items
        Permission::create(['name' => 'can add']);
        Permission::create(['name' => 'can view']);
        Permission::create(['name' => 'can reset user password']);
        Permission::create(['name' => 'can edit']);
        Permission::create(['name' => 'can destroy']);

        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(['can add', 'can view', 'can edit', 'can destroy', 'can reset user password']);

        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(['can add', 'can view', 'can destroy', 'can reset user password']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['can add', 'can view', 'can destroy', 'can reset user password']);

        $librarianRole = Role::create(['name' => 'librarian']);
        $librarianRole->givePermissionTo(['can add', 'can view', 'can update', 'can destroy']);
        $useRole = Role::create(['name' => 'user']);
        $useRole->givePermissionTo(['can view']);

//
        $SuperAdmin = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'SuperAdmin',
            'surname' => 'BinaniAir',
            'email' => 'super-admin@binaniair.com',
            'phone' => '09000023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $SuperAdmin->assignRole('super-admin');
        $SuperAdmin->givePermissionTo(['can add', 'can view', 'can edit', 'can destroy']);

        $Admin = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'Admin',
            'surname' => 'BinaniAir',
            'email' => 'admin@binaniair.com',
            'phone' => '09200023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $Admin->assignRole('admin');
        $Admin->givePermissionTo(['can add', 'can view', 'can edit', 'can destroy']);
//
        $librarian = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'Librarian',
            'surname' => 'BinaniAir',
            'email' => 'librarian@binaniair.com',
            'phone' => '09130023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $librarian->assignRole('librarian');
        $librarian->givePermissionTo(['can add', 'can view', 'can edit']);
//
        $user = User::create([
            'uid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'User',
            'surname' => 'BinaniAir',
            'email' => 'user@binaniair.com',
            'phone' => '09100023456',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('user');
        $user->givePermissionTo(['can view']);


        $manual = Manuals::create([
            'mid' => uuid_create(UUID_TYPE_DEFAULT),
            'name' => 'Company Manuals',
        ]);

        $manuals = Manuals::all();
        foreach ($manuals as $manualId) {
            $permissionName = "access-manual-{$manualId->name}";
            Permission::Create(['name' => $permissionName]);
            $users = User::role(['Admin', 'Librarian'])->get();

            foreach ($users as $user) {
                // Assign the permission to each user
                $user->givePermissionTo($permissionName);
            }
        }


        $manuals = ManualsItem::all();
        foreach ($manuals as $manualId) {
            $getParentManual = Manuals::where('mid', $manualId->manual_uid)->first();;
            $permissionName = "access-manual-{$getParentManual->name}.{$manualId->name}";
            Permission::Create(['name' => $permissionName]);
            $users = User::role(['Admin', 'Librarian'])->get();
            foreach ($users as $user) {
                // Assign the permission to each user
                $user->givePermissionTo($permissionName);
            }
        }



//      $manual = Manuals::create([
//            'mid' => uuid_create(UUID_TYPE_DEFAULT),
//            'name' => 'NCAA HardCopy',
//            'type' => 1
//        ]);


    }
}
