<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Muestra una lista paginada de productos.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Por defecto 10 por página
        
        $products = Product::with(['images', 'supplier', 'category'])
            ->paginate($perPage);

        // Transformar la respuesta para incluir las imágenes de forma más clara
        $products->getCollection()->transform(function ($product) {
            $data = $product->toArray();
            
            // Agregar las imágenes del modelo ProductImage
            $data['images'] = $product->images->map(function ($image) {
                return [
                    'id' => $image->id_product_image,
                    'url' => $image->url
                ];
            });
            
            // Si existe url_imagen (array), mantenerlo por compatibilidad
            if ($product->url_imagen && is_array($product->url_imagen)) {
                $data['legacy_images'] = $product->url_imagen;
            }
            
            return $data;
        });

        return response()->json($products);
    }

    /**
     * Muestra un producto específico con sus imágenes.
     */
    public function show($id)
    {
        $product = Product::with(['images', 'supplier', 'category'])->find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $data = $product->toArray();
        
        // Agregar las imágenes del modelo ProductImage
        $data['images'] = $product->images->map(function ($image) {
            return [
                'id' => $image->id_product_image,
                'url' => $image->url
            ];
        });
        
        // Si existe url_imagen (array), mantenerlo por compatibilidad
        if ($product->url_imagen && is_array($product->url_imagen)) {
            $data['legacy_images'] = $product->url_imagen;
        }

        return response()->json($data);
    }
}
