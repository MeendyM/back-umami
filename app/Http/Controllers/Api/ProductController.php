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
        $products = Product::paginate($perPage);
        return response()->json($products);
    }
}
