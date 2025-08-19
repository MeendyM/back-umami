<?php

namespace Database\Seeders;

use App\Enums\TypeUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'name' => 'Admin',
            'id_institution' => 1,
            "type" => TypeUser::ADMIN->value
        ]);

        User::create([
            'email' => 'Are271@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'name' => 'Areli Admin',
            'id_institution' => 1,
            "type" => TypeUser::ADMIN->value
        ]);
    }
}
