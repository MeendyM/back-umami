<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

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
        //sgregar el apartado de los customs y la cantida personalizada
        
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
