<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected static ?string $password;
    public function run(): void
    {
//        $user = User::create([
//            'name' => 'Bin',
//            'surname' => 'Ani',
//            'email' => 'test@example.com',
//            'phone' => '09000023456',
//            'password' => static::$password ??= Hash::make('password'),
//            'remember_token' => Str::random(10),
//        ]);
//        $user->assignRole('admin', 'user');
    }
}
