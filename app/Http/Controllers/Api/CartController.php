<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isArray;

class CartController extends Controller
{
    //obtener el carrito del usuario autenticado
    public function getByUser(Request $request)
    {
        $userId = $request->user()->id_user;

        // Obtener los items del carrito del usuario
        $cartItems = Cart::where('id_user', $userId)
            ->with(['orderItem.product'])
            ->get();

        // Verificar si el carrito está vacío
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], 404);
        }

        // Retornar los items del carrito
        return response()->json($cartItems, 200);
    }

    //agregar item
    public function addItem(Request $request)
    {
        $userId = $request->user()->id_user;

        $request->validate([
            'id_product' => 'required|integer|exists:products,id_product',
            'customs' => 'nullable|array',
            'customs.*' => 'string',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::find($request->id_product);
        $quantity = $request->input('quantity', 1); // Default: 1
        $customizations = $request->input('customs', []);

        // Caso base: Buscar si el producto ya está en el carrito del usuario
        $existingItem = OrderItem::where('id_user', $userId)
            ->where('id_product', $product->id_product)
            ->first();

        if ($existingItem) {
            if ($quantity === 1) {
                // CASO 1: No se especificó cantidad, se suma 1
                $existingItem->quantity += 1;
                $existingItem->subtotal += $product->price;

                $new = $customizations;
                $current = $existingItem->custom_text;

                $resultado = $current + $new;

                $existingItem->custom_text = $resultado;

                $existingItem->save();

                return response()->json([
                    'message' => 'Producto actualizado en el carrito',
                    'item' => $existingItem
                ]);
            } else {
                // CASO 3: Se especificó cantidad personalizada, se suma a la existente
                $existingItem->quantity += $quantity;
                $existingItem->subtotal += $product->price * $quantity;

                $existingItem->save();


                Log::debug($existingItem->custom_text);
            }

            $existingItem->save();

            return response()->json([
                'message' => 'Producto actualizado en el carrito',
                'item' => $existingItem
            ], 200);
        }

        // CASO 2 y 4: El producto aún no está en el carrito
        $newItem = OrderItem::create([
            'id_user' => $userId,
            'id_product' => $product->id_product,
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
            'custom_text' => $customizations,
        ]);

        Cart::create([
            'id_user' => $userId,
            'id_order_item' => $newItem->id_order_item,
        ]);

        return response()->json([
            'message' => 'Producto agregado al carrito',
            'item' => $newItem
        ], 201);
    }


    //remover item
    public function removeItem(Request $request)
    {
        $userId = $request->user()->id_user;

        // Buscar el item en 'order_items' del usuario
        $orderItem = OrderItem::where('id_user', $userId)
            ->where('id_product', $request->id_product)
            ->first();

        if (!$orderItem) {
            return response()->json(['message' => 'Item no encontrado en el carrito'], 404);
        }

        //Condicion para verificar si la cantidad es mayor a 1
        if ($orderItem->quantity > 1) {

            $pricePerItem = Product::find($orderItem->id_product)->price;

            //Si la cantidad es mayor a 1, se reduce la cantidad y se resta el subtotal
            $orderItem->quantity -= 1; // Decrementar la cantidad
            $orderItem->subtotal -= $pricePerItem;
            $orderItem->save();

            return response()->json([
                'message' => 'Cantidad del item reducida en el carrito',
                'item' => $orderItem
            ], 200);
        } else {
            //Si la cantidad es 1, se elimina el item del carrito
            Cart::where('id_order_item', $request->id_order_item)->delete();

            //Y despues se elimina el item del order_item
            $orderItem->delete();

            return response()->json([
                'message' => 'Item eliminado del carrito'
            ], 200);
        }
    }
}
