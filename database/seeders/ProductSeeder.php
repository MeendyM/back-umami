<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener proveedores y categorías
        $suppliers = Supplier::all();
        $categories = Category::all();

        // Asegúrate de tener por lo menos un proveedor y una categoría en la base de datos
        if ($suppliers->isEmpty() || $categories->isEmpty()) {
            $this->command->info('No hay proveedores o categorías para asignar productos');
            return;
        }

        // Crear productos
        Product::create([
            'name' => 'Cuchillo de Chef 8" Profesional',
            'description' => 'Cuchillo profesional para chef, hoja de 8 pulgadas, acero inoxidable.',
            'price' => 120.00,
            'url_imagen' => 'https://example.com/chef-knife.jpg',
            'id_supplier' => $suppliers->first()->id_supplier, // Asignar primer proveedor
            'id_category' => $categories->first()->id_category, // Asignar primera categoría
        ]);

        Product::create([
            'name' => 'Cuchillo Santoku 7" Premium',
            'description' => 'Cuchillo premium Santoku de 7 pulgadas, ideal para picar y cortar en dados.',
            'price' => 100.00,
            'url_imagen' => 'https://example.com/santoku-knife.jpg',
            'id_supplier' => $suppliers->skip(1)->first()->id_supplier, // Asignar segundo proveedor
            'id_category' => $categories->skip(1)->first()->id_category, // Asignar segunda categoría
        ]);

        Product::create([
            'name' => 'Set de Cuchillos - 6 Piezas',
            'description' => 'Set de cuchillos de 6 piezas, incluye una variedad de hojas para diferentes usos.',
            'price' => 200.00,
            'url_imagen' => 'https://example.com/knife-set.jpg',
            'id_supplier' => $suppliers->skip(2)->first()->id_supplier, // Asignar tercer proveedor
            'id_category' => $categories->first()->id_category,
        ]);

        Product::create([
            'name' => 'Delantal de Cocina Profesional',
            'description' => 'Delantal de cocina duradero y elegante para profesionales.',
            'price' => 30.00,
            'url_imagen' => 'https://example.com/cooking-apron.jpg',
            'id_supplier' => $suppliers->skip(3)->first()->id_supplier, // Asignar cuarto proveedor
            'id_category' => $categories->skip(1)->first()->id_category,
        ]);

        Product::create([
            'name' => 'Set de Sales Gourmet',
            'description' => 'Colección de sales gourmet premium para entusiastas culinarios.',
            'price' => 45.00,
            'url_imagen' => 'https://example.com/gourmet-salt.jpg',
            'id_supplier' => $suppliers->skip(4)->first()->id_supplier, // Asignar quinto proveedor
            'id_category' => $categories->first()->id_category,
        ]);
    }
}
