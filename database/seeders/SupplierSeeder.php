<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            'Suoliser',
            'ChefPro',
            'Cocina Elite',
            'GastroEquipos MX',
            'Distribuciones Gourmet'
        ];

        foreach ($suppliers as $name) {
            Supplier::create(['name' => $name]);
        }
    }
}
