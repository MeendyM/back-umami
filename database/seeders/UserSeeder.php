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
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'name' => 'Admin',
            'id_rol' => 2,
            'id_institution' => 1,
        ]);

        User::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'name' => 'Student',
            'id_rol' => 2,
        ]);

        User::create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'name' => 'Buyer',
            'id_rol' => 3,
        ]);
    }
}
