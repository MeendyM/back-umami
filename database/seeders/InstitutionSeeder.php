<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            'Tecnológico de Teziutlán',
            'Universidad del Valle de Puebla',
            'Instituto Culinario de Veracruz',
            'Universidad de Oriente Puebla',
            'Instituto Gastronómico del Sureste'
        ];

        foreach ($institutions as $name) {
            Institution::create(['name' => $name]);
        }
    }
}
