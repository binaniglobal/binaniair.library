<?php

namespace Database\Seeders;

use App\Models\ManualItemContent;
use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected static ?string $password;

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

        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(['view-home', 'create-manual', 'view-manual', 'edit-manual', 'destroy-manual', 'view-user', 'edit-user', 'destroy-user', 'reset-password', 'view-report', 'generate-report']);

        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(['view-home', 'create-manual', 'view-manual', 'edit-manual', 'destroy-manual', 'create-user', 'view-user', 'edit-user', 'destroy-user', 'reset-password']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view-home', 'create-manual', 'view-manual', 'edit-manual', 'destroy-manual', 'create-user', 'view-user', 'edit-user', 'destroy-user', 'reset-password']);

        $useRole = Role::create(['name' => 'user']);
        $useRole->givePermissionTo(['view-manual']);


//        foreach (Manuals::all() as $manual) {
//            Permission::create(['name' => $manual->name]);
//            foreach (ManualsItem::all() as $manualItem) {
//                if ($manualItem->file_type == 'Folder') {
//                    Permission::create(['name' => $manual->name . '.' . $manualItem->name]);
//                    foreach (ManualItemContent::all() as $manualItemContent) {
//                        Permission::create(['name' => $manual->name . '.' . $manualItem->name . '.' . $manualItemContent->name]);
//                    }
//                }
//                if ($manualItem->file_type == 'application/pdf') {
//                    Permission::create(['name' => $manual->name . '.' . $manualItem->name]);
//                }
//
//            }
//        }
    }
}
