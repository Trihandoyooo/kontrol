<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nik' => '1111111111',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'nik' => '2222222222',
            'name' => 'Ketua User',
            'email' => 'ketua@example.com',
            'password' => Hash::make('password'),
            'role' => 'ketua',
        ]);

        User::create([
            'nik' => '3333333333',
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
