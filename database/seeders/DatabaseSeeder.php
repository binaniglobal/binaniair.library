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

//        $this->call(PermissionSeeder::class);
//        $this->call(ProductionSeeder::class);

    }
}
