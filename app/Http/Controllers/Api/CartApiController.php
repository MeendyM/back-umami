<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class CartApiController extends Controller
{
    public function addItem(Request $request)
    {
        $userId = $request->user()->id_user;

        $request->validate([
            'id_product' => 'required|integer|exists:products,id_product',
        ]);

        // Buscar el producto
        $product = Product::find($request->id_product);
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        //Verificar si el producto ya está en el 'order_items' del usuario
        $verifyItem = OrderItem::where('id_user', $userId)
            ->where('id_product', $product->id_product)
            ->first();
            
        if ($verifyItem) {

            //Si la verificacion encuentra el item, se actualiza la cantidad y el subtotal
            $verifyItem->quantity += 1; // Incrementar la cantidad
            $verifyItem->subtotal += $product->price; // Actualizar el subtotal
            $verifyItem->save();

            //Retornar la respuesta con el item actualizado
            return response()->json([
                'message' => 'Producto actualizado en el carrito',
                'item' => $verifyItem
            ], 200);
        } else {
            //Si la verificacion no encuentra el item se crea uno nuevo

            // Crear item 
            $orderItem = OrderItem::create([
                'id_user' => $userId,
                'id_product' => $product->id_product,
                'quantity' => 1,
                'subtotal' => $product->price,
            ]);

            // Asociar el item al carrito del usuario
            Cart::create([
                'id_user' => $userId,
                'id_order_item' => $orderItem->id_order_item,
            ]);

            return response()->json([
                'message' => 'Producto agregado al carrito',
                'item' => $orderItem
            ], 201);
        }
    }
}
