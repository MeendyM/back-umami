<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Set;
use Illuminate\Http\Request;

class SetApiController extends Controller
{
    public function getSets()
    {
        $sets = Set::with(['products.images', 'products.supplier', 'supplier'])
            ->get(['id_set', 'name', 'description', 'only_in_set', 'url_image', 'price', 'id_supplier']);

        $sets = $sets->map(function ($set) {
            return [
                'id_set' => $set->id_set,
                'name' => $set->name,
                'only_in_set' => $set->only_in_set,
                'description' => $set->description,
                'price' => $set->price,
                'url_image' => $set->url_image, // Imagen principal del set
                'supplier' => $set->supplier ? [
                    'id' => $set->supplier->id_supplier,
                    'name' => $set->supplier->name
                ] : null,
                'products' => $set->products->map(function ($product) {
                    return [
                        'id_product' => $product->id_product,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'is_customized' => $product->is_customized,
                        'images' => $product->images->map(function ($image) {
                            return [
                                'id' => $image->id_product_image,
                                'url' => $image->url
                            ];
                        }),
                        // Mantener compatibilidad con url_imagen si existe
                        'legacy_images' => $product->url_imagen && is_array($product->url_imagen) 
                            ? $product->url_imagen 
                            : [],
                        'supplier' => $product->supplier ? [
                            'id' => $product->supplier->id_supplier,
                            'name' => $product->supplier->name
                        ] : null,
                    ];
                }),
                'product_ids' => $set->products->pluck('id_product'),
            ];
        });

        return response()->json(['sets' => $sets], 200);
    }

    public function getProductsFromSets($id_set)
    {
        $set = Set::with(['products.images', 'products.supplier', 'supplier'])
            ->find($id_set);

        if (!$set) {
            return response()->json(['message' => 'Set not found'], 404);
        }

        // Transformar la respuesta para incluir las imágenes
        $setData = [
            'id_set' => $set->id_set,
            'name' => $set->name,
            'description' => $set->description,
            'price' => $set->price,
            'only_in_set' => $set->only_in_set,
            'url_image' => $set->url_image, // Imagen principal del set
            'supplier' => $set->supplier ? [
                'id' => $set->supplier->id_supplier,
                'name' => $set->supplier->name
            ] : null,
            'products' => $set->products->map(function ($product) {
                return [
                    'id_product' => $product->id_product,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'is_customized' => $product->is_customized,
                    'only_in_set' => $product->only_in_set,
                    'images' => $product->images->map(function ($image) {
                        return [
                            'id' => $image->id_product_image,
                            'url' => $image->url
                        ];
                    }),
                    // Mantener compatibilidad con url_imagen si existe
                    'legacy_images' => $product->url_imagen && is_array($product->url_imagen) 
                        ? $product->url_imagen 
                        : [],
                    'supplier' => $product->supplier ? [
                        'id' => $product->supplier->id_supplier,
                        'name' => $product->supplier->name
                    ] : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id_category,
                        'name' => $product->category->name
                    ] : null,
                ];
            })
        ];

        return response()->json(['set' => $setData], 200);
    }
}
