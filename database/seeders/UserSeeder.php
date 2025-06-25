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
            'password' => Hash::make('password'),
            'name' => 'Admin',
            'id_institution' => 1,
            "type" => TypeUser::ADMIN->value
            //Poner siempre el de mayusculas
        ]);

        User::create([
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'name' => 'Student',
            'id_institution' => 2,
            'type' => TypeUser::STUDENT->value
        ]);

        User::create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'name' => 'Buyer',
            'id_institution' => 3,
            'type' => TypeUser::STUDENT->value
        ]);

        User::create([
            'email' => 'embh2910@gmail.com',
            'password' => Hash::make('1234'),
            'name' => 'Ruben Ramirez Hernandez',
            'id_institution' => 4,
            'type' => TypeUser::STUDENT->value
        ]);
    }
}
