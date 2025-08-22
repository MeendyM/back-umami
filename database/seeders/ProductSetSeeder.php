<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Set;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el proveedor Victorinox (debe existir previamente)
        $victorinoxSupplier = Supplier::where('name', 'Victorinox')->first();
        
        if (!$victorinoxSupplier) {
            // Crear el proveedor si no existe
            $victorinoxSupplier = Supplier::create([
                'name' => 'Victorinox',
                //'contact_info' => 'Proveedor de cuchillos profesionales',
            ]);
        }

        // Crear los sets con sus precios
        $paqueteBasico = Set::create([
            'name' => 'Paquete Basico',
            'description' => 'Set básico de cuchillos para chef',
            'price' => 2430,
            'only_in_set' => true,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        $paqueteV0001266 = Set::create([
            'name' => 'Paquete V0001266',
            'description' => 'Set completo profesional V0001266',
            'price' => 4300,
            'only_in_set' => true,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        $setTriA = Set::create([
            'name' => 'Set TriA',
            'description' => 'Set TriA para chef profesional',
            'price' => 3942,
            'only_in_set' => true,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        $paqueteComplementario = Set::create([
            'name' => 'Paquete Complementario',
            'description' => 'Set complementario con herramientas adicionales',
            'price' => 0, // Sin precio definido
            'only_in_set' => true,
            'id_supplier' => $victorinoxSupplier->id_supplier,
        ]);

        // Productos para Paquete Basico
        $productsBasico = [
            'Funda porta cuchillos en nylon, para chef reforzado',
            'Cuchillo Swiss Classic formador curvo, 6cm, negro',
            'Cuchillo Swiss Classic mondador punta, 8cm, negro',
            'Cuchillo para chef 25cm, mango nylon negro',
            'Pelapapas negro',
            'Afilador duo',
        ];

        $this->createProductsForSet($productsBasico, $paqueteBasico, $victorinoxSupplier->id_supplier, true);

        // Productos para Paquete V0001266
        $productsV0001266 = [
            'Funda porta cuchillos en nylon, para chef reforzado',
            'Cuchillo Swiss Classic formador curvo, 6cm, negro',
            'Cuchillo Swiss Classic mondador punta, 8cm, negro',
            'Cuchillo filetero flexible 20cm, mango nylon negro',
            'Cuchillo deshuesador recto 15cm, fibrox negro',
            'Cuchillo para pan dentado 21cm, mango nylon, blister',
            'Cuchillo para chef 25cm, mango nylon negro',
            'Pelapapas negro',
            '5710-300 (7.8513) chaira redonda 12" mango nylon negro',
        ];

        $this->createProductsForSet($productsV0001266, $paqueteV0001266, $victorinoxSupplier->id_supplier, true);

        // Productos para Set TriA
        $productsTriA = [
            'Funda porta cuchillos en nylon, para chef reforzado',
            'Cuchillo Swiss Classic formador curvo, 6cm, negro',
            'Cuchillo Swiss Classic mondador punta, 8cm, negro',
            'Cuchillo para chef 25cm, mango nylon negro',
            'Cuchillo para pan dentado 21cm, mango nylon, blister',
            'Cuchillo filetero flexible 20cm, mango nylon negro',
            'Pelapapas negro',
            'Afilador duo',
        ];

        $this->createProductsForSet($productsTriA, $setTriA, $victorinoxSupplier->id_supplier, true);

        // Productos para Paquete Complementario
        $productsComplementario = [
            'Funda reforzada',
            'Cuchillo Swiss Classic formador curvo, 6cm, negro',
            'Cuchillo Swiss Classic mondador punta, 8cm, negro',
            'Cuchillo filetero flexible 20cm, mango nylon negro',
            'Cuchillo deshuesador recto 15cm, fibrox negro',
            'Cuchillo para pan dentado 21cm, mango nylon, blister',
            'Cuchillo para chef 25cm, mango nylon negro',
            'Pelapapas negro',
            'Chaira redonda 12" mango nylon negro',
            'Tabla para picar',
            'Espatula miserable 30 cm',
            'Termometro digital',
            'Espatula angular 26 cm',
            'Set de manga con duyas',
            'Batidor globo 30 cm',
            'Descorchador',
        ];

        $this->createProductsForSet($productsComplementario, $paqueteComplementario, $victorinoxSupplier->id_supplier, true);
    }

    /**
     * Crear productos para un set específico
     */
    private function createProductsForSet(array $productNames, Set $set, int $supplierId, bool $onlyInSet = true)
    {
        foreach ($productNames as $name) {
            // Verificar si el producto ya existe
            $existingProduct = Product::where('name', $name)->first();
            
            if ($existingProduct) {
                // Si el producto ya existe, solo lo asociamos al set
                $set->products()->attach($existingProduct->id_product);
            } else {
                // Si no existe, lo creamos
                $product = Product::create([
                    'name' => $name,
                    'description' => $name,
                    'price' => 0, // Los productos del set tienen precio 0, el precio está en el set
                    //'url_imagen' => [],
                    'id_supplier' => $supplierId,
                    'only_in_set' => $onlyInSet,
                ]);
                
                // Asociar el producto al set
                $set->products()->attach($product->id_product);
            }
        }
    }
}
