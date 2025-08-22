<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Set;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $victorinoxSupplier = Supplier::where('name', 'Victorinox')->first();
        $paqueteBasico = Set::where('name', 'Paquete Basico')->first();
        $paqueteV0001266 = Set::where('name', 'Paquete V0001266')->first();
        $setTriA = Set::where('name', 'Set TriA')->first();
        $paqueteComplementario = Set::where('name', 'Paquete Complementario')->first();

        // Products for Paquete Basico
        $productsBasico = [
            'Funda porta cuchillos en nylon, para chef reforzado' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic formador curvo, 6cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic mondador punta, 8cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para chef 25cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Pelapapas negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Afilador duo' => ['description' => null, 'price' => 0, 'only_in_set' => true],
        ];

        foreach ($productsBasico as $name => $data) {
            $product = Product::create([
                'name' => $name,
                'description' => $data['description'],
                'price' => $data['price'],
                'url_imagen' => [],
                'id_supplier' => $victorinoxSupplier->id_supplier,
                'only_in_set' => $data['only_in_set'],
            ]);
            $paqueteBasico->products()->attach($product->id_product);
        }

        // Products for Paquete V0001266
        $productsV0001266 = [
            'Funda porta cuchillos en nylon, para chef reforzado' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic formador curvo, 6cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic mondador punta, 8cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo filetero flexible 20cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo deshuesador recto 15cm, fibrox negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para pan dentado 21cm, mango nylon, blister' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para chef 25cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Pelapapas negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            '5710-300 (7.8513) Chaira redonda 12" mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
        ];

        foreach ($productsV0001266 as $name => $data) {
            $product = Product::create([
                'name' => $name,
                'description' => $data['description'],
                'price' => $data['price'],
                'url_imagen' => [],
                'id_supplier' => $victorinoxSupplier->id_supplier,
                'only_in_set' => $data['only_in_set'],
            ]);
            $paqueteV0001266->products()->attach($product->id_product);
        }

        // Products for Set TriA
        $productsTriA = [
            'Funda porta cuchillos en nylon, para chef reforzado' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic formador curvo, 6cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic mondador punta, 8cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para chef 25cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para pan dentado 21cm, mango nylon, blister' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo filetero flexible 20cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Pelapapas negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Afilador duo' => ['description' => null, 'price' => 0, 'only_in_set' => true],
        ];

        foreach ($productsTriA as $name => $data) {
            $product = Product::create([
                'name' => $name,
                'description' => $data['description'],
                'price' => $data['price'],
                'url_imagen' => [],
                'id_supplier' => $victorinoxSupplier->id_supplier,
                'only_in_set' => $data['only_in_set'],
            ]);
            $setTriA->products()->attach($product->id_product);
        }

        // Products for Paquete Complementario
        $productsComplementario = [
            'Funda reforzada' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic formador curvo, 6cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo Swiss Classic mondador punta, 8cm, negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo filetero flexible 20cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo deshuesador recto 15cm, fibrox negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para pan dentado 21cm, mango nylon, blister' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Cuchillo para chef 25cm, mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Pelapapas negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Chaira redonda 12" mango nylon negro' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Tabla para picar' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Espatula miserable 30 cm' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Termometro digital' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Espatula angular 26 cm' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Set de manga con duyas' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Batidor globo 30 cm' => ['description' => null, 'price' => 0, 'only_in_set' => true],
            'Descorchador' => ['description' => null, 'price' => 0, 'only_in_set' => true],
        ];

        foreach ($productsComplementario as $name => $data) {
            $product = Product::create([
                'name' => $name,
                'description' => $data['description'],
                'price' => $data['price'],
                'url_imagen' => [],
                'id_supplier' => $victorinoxSupplier->id_supplier,
                'only_in_set' => $data['only_in_set'],
            ]);
            $paqueteComplementario->products()->attach($product->id_product);
        }
    }
}