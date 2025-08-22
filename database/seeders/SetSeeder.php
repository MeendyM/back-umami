<?php

namespace Database\Seeders;

use App\Models\Set;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $victorinoxSupplier = Supplier::where('name', 'Victorinox')->first();

        Set::create([
            'name' => 'Paquete Basico',
            'price' => 2430,
            'only_in_set' => false,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        Set::create([
            'name' => 'Paquete V0001266',
            'price' => 4300,
            'only_in_set' => false,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        Set::create([
            'name' => 'Set TriA',
            'price' => 3942,
            'only_in_set' => false,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        Set::create([
            'name' => 'Paquete Complementario',
            'price' => 0,
            'only_in_set' => true,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);
    }
}