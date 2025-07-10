<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Set;
use App\Models\Product;
use App\Models\ProductSet;

class ProductSetSeeder extends Seeder
{
    public function run(): void
    {
        // Crear sets
        $set1 = Set::create([
            'name' => 'Set Umami',
            'description' => 'Incluye utensilios esenciales para la cocina.',
            //'url_image' => 'https://example.com/basic-kitchen-set.jpg',
        ]);

        $set2 = Set::create([
            'name' => 'Set de Chef Profesional',
            'description' => 'Herramientas premium para chefs profesionales.',
            //'url_image' => 'https://example.com/pro-chef-set.jpg',
        ]);

        $set3 = Set::create([
            'name' => 'Set Gourmet',
            'description' => 'Productos exclusivos para entusiastas culinarios.',
            //'url_image' => 'https://example.com/gourmet-set.jpg',
        ]);

        // Obtener productos existentes
        $products = Product::all();

        // Asignar productos a los sets
        foreach ($products->take(3) as $product) {
            ProductSet::create([
                'id_set' => $set1->id_set,
                'id_product' => $product->id_product,
            ]);
        }

        foreach ($products->skip(3)->take(3) as $product) {
            ProductSet::create([
                'id_set' => $set2->id_set,
                'id_product' => $product->id_product,
            ]);
        }

        foreach ($products->skip(6)->take(3) as $product) {
            ProductSet::create([
                'id_set' => $set3->id_set,
                'id_product' => $product->id_product,
            ]);
        }
    }
}
