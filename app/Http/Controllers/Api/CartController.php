<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusOrder;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
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

        Log::info('Intentando agregar al carrito', [
            'user_id' => $userId,
            'request' => $request->all()
        ]);

        $request->validate([
            'id_product' => 'required|integer|exists:products,id_product',
            'customs' => 'nullable|array',
            'customs.*' => 'string',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::find($request->id_product);
        $quantity = $request->input('quantity', 1); // Default: 1
        $customizations = $request->input('customs', []);

        Log::info('Producto encontrado', [
            'product' => $product
        ]);

        // Caso base: Buscar si el producto ya está en el carrito del usuario
        $existingItem = OrderItem::where('id_user', $userId)
            ->where('id_product', $product->id_product)
            ->whereNull('id_order')
            ->first();

        Log::info('Item existente en carrito', [
            'existingItem' => $existingItem
        ]);

        // Determinar si el producto es personalizado
        $isCustomized = (bool)($product->is_customized ?? false);

        if ($existingItem) {
            if ($quantity === 1) {
                // CASO 1: No se especificó cantidad, se suma 1
                $existingItem->quantity += 1;
                $existingItem->subtotal += $product->price;

                $new = $customizations;
                $current = $existingItem->custom_text;

                // Unir los textos personalizados correctamente
                $resultado = array_merge($current ?? [], $new ?? []);

                $existingItem->custom_text = $resultado;
                $existingItem->is_customized = $isCustomized;

                Log::info('Actualizando item existente (sumando 1)', [
                    'item' => $existingItem
                ]);

                $existingItem->save();

                return response()->json([
                    'message' => 'Producto actualizado en el carrito',
                    'item' => $existingItem
                ]);
            } else {
                // CASO 3: Se especificó cantidad personalizada, se suma a la existente
                $existingItem->quantity += $quantity;
                $existingItem->subtotal += $product->price * $quantity;

                $new = $customizations;
                $current = $existingItem->custom_text;

                // Unir los textos personalizados correctamente
                $resultado = array_merge($current ?? [], $new ?? []);

                $existingItem->custom_text = $resultado;
                $existingItem->is_customized = $isCustomized;

                Log::info('Actualizando item existente (sumando cantidad personalizada)', [
                    'item' => $existingItem
                ]);

                $existingItem->save();

                Log::debug($existingItem->custom_text);
            }

            $existingItem->save();

            return response()->json([
                'message' => 'Producto actualizado en el carrito',
                'item' => $existingItem
            ], 200);
        }

        // Si existe un item pero ya tiene id_order, crear uno nuevo (nuevo ciclo de carrito)
        $newItem = OrderItem::create([
            'id_user' => $userId,
            'id_product' => $product->id_product,
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
            'custom_text' => $customizations,
            'is_customized' => $isCustomized,
        ]);

        Log::info('Creando nuevo item en carrito', [
            'newItem' => $newItem
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

            $customs = $orderItem->custom_text;

            // verificar si el array de customs tiene algo
            if (empty($customs)) {
                return response()->json(['message' => 'No hay customizaciones para eliminar'], 400);
            }

            // Si se especifica una clave, eliminar esa
            if ($request->has('custom_key')) {
                $custom_key = $request->custom_key;

                if (array_key_exists($custom_key, $customs)) {
                    unset($customs[$custom_key]);
                } else {
                    return response()->json(['message' => 'Clave no encontrada en custom_text'], 404);
                }
            } else {
                // Si no eliminar la ultima entrada conservando claves
                end($customs);               // Mueve el puntero interno al final
                $lastKey = key($customs);    // Obtiene la ultima clave
                unset($customs[$lastKey]);   // Elimina la entrada
            }

            //Si la cantidad es mayor a 1, se reduce la cantidad y se resta el subtotal
            $orderItem->quantity -= 1; // Decrementar la cantidad
            $orderItem->subtotal -= $pricePerItem;
            $orderItem->custom_text = $customs;
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

    public function edit(Request $request)
    {
        $userId = $request->user()->id_user;

        Log::info('Intentando editar item del carrito', [
            'user_id' => $userId,
            'request' => $request->all()
        ]);

        $validated = $request->validate([
            'id_order_item' => 'required|integer',
            'cantidad_a_retirar' => 'required|integer|min:1',
            'custom_key' => 'required|array'
        ]);

        $orderItem = OrderItem::where('id_user', $userId)
            ->where('id_order_item', $validated['id_order_item'])
            ->first();

        Log::info('Item encontrado para editar', [
            'orderItem' => $orderItem
        ]);

        if (!$orderItem) {
            Log::warning('Item no encontrado en el carrito para editar', [
                'user_id' => $userId,
                'id_order_item' => $validated['id_order_item']
            ]);
            return response()->json(['message' => 'Item no encontrado en el carrito'], 404);
        }

        $cantidadARetirar = $validated['cantidad_a_retirar'];
        $currentQty = $orderItem->quantity;
        $customs = $orderItem->custom_text ?? [];

        // 1. Verificar que no se intente eliminar más productos de los que hay
        if ($cantidadARetirar >= $currentQty) {
            Log::warning('Intento de eliminar más productos de los que hay', [
                'cantidad_a_retirar' => $cantidadARetirar,
                'currentQty' => $currentQty
            ]);
            return response()->json([
                'message' => 'No puedes eliminar más productos de los que hay en el carrito'
            ], 422);
        }

        // 2. Verificar que se envíen tantas customizaciones como cantidad a retirar
        if (count($validated['custom_key']) !== $cantidadARetirar) {
            Log::warning('Cantidad de customizaciones a eliminar no coincide con la cantidad a retirar', [
                'custom_key_count' => count($validated['custom_key']),
                'cantidad_a_retirar' => $cantidadARetirar
            ]);
            return response()->json([
                'message' => 'La cantidad de customizaciones a eliminar debe coincidir con la cantidad que deseas retirar'
            ], 422);
        }

        // 3. Verificar que todas las claves a eliminar existan
        foreach ($validated['custom_key'] as $key) {
            if (!array_key_exists($key, $customs)) {
                Log::warning('Clave de customización no existe', [
                    'key' => $key,
                    'customs' => $customs
                ]);
                return response()->json([
                    'message' => "La clave '{$key}' no existe en custom_text"
                ], 422);
            }
        }

        // 4. Eliminar las customizaciones
        foreach ($validated['custom_key'] as $key) {
            unset($customs[$key]);
        }

        // 5. Restar cantidad
        $orderItem->quantity -= $cantidadARetirar;

        // 6. Recalcular subtotal (suponiendo que tienes el modelo Product y precio unitario)
        $product = Product::find($orderItem->id_product);
        if (!$product) {
            Log::warning('Producto no encontrado en catálogo al editar', [
                'id_product' => $orderItem->id_product
            ]);
            return response()->json(['message' => 'Producto no encontrado en catálogo'], 404);
        }

        $orderItem->subtotal = $orderItem->quantity * $product->price;

        // 7. Guardar cambios
        $orderItem->custom_text = $customs;
        $orderItem->save();

        Log::info('Producto actualizado correctamente en el carrito', [
            'orderItem' => $orderItem
        ]);

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'quantity' => $orderItem->quantity,
            'custom_text' => $orderItem->custom_text,
            'subtotal' => $orderItem->subtotal
        ]);
    }

    public function clear(Request $request)
    {
        $userId = $request->user()->id_user;

        // Eliminar todos los items del carrito del usuario
        OrderItem::where('id_user', $userId)->delete();
        Cart::where('id_user', $userId)->delete();


        return response()->json(['message' => 'Carrito vaciado correctamente'], 200);
    }

    public function createOrder(Request $request)
    {
        $userId = $request->user()->id_user;

        // 1. Obtener todos los items del usuario sin orden asignada
        $orderItems = OrderItem::where('id_user', $userId)
            ->whereNull('id_order')
            ->get();

        if ($orderItems->isEmpty()) {
            return response()->json(['message' => 'No hay productos en el carrito para crear la orden'], 400);
        }

        // 2. Calcular el total
        $total = $orderItems->sum('subtotal');

        $newOrder = Order::create([
            'id_user' => $userId,
            'status' => StatusOrder::REQUESTED->value, // Estado inicial
            'total' => $total,
        ]);

        // 4. Actualizar los order_items con el id de la nueva orden
        foreach ($orderItems as $item) {
            $item->id_order = $newOrder->id_order;
            $item->save();
        }

        // 5. Limpiar el carrito temporal
        Cart::where('id_user', $userId)->delete();

        return response()->json([
            'message' => 'Orden solicitada exitosamente',
            'order_id' => $newOrder->id_order,
            'total' => $total,
        ]);
    }

    public function editCustomTexts(Request $request)
    {
        $userId = $request->user()->id_user;

        Log::info('Intentando editar textos personalizados', [
            'user_id' => $userId,
            'request' => $request->all()
        ]);

        $validated = $request->validate([
            'id_order_item' => 'required|integer',
            'custom_text' => 'required|array',
            'custom_text.*' => 'string',
        ]);

        $orderItem = OrderItem::where('id_user', $userId)
            ->where('id_order_item', $validated['id_order_item'])
            ->first();

        Log::info('Item encontrado para editar custom_text', [
            'orderItem' => $orderItem
        ]);

        if (!$orderItem) {
            Log::warning('Item no encontrado en el carrito para editar custom_text', [
                'user_id' => $userId,
                'id_order_item' => $validated['id_order_item']
            ]);
            return response()->json(['message' => 'Item no encontrado en el carrito'], 404);
        }

        // Si el producto es personalizado, la cantidad debe coincidir con la cantidad de textos
        /*if ($orderItem->is_customized) {
            if (count($validated['custom_text']) !== $orderItem->quantity) {
                Log::warning('Cantidad de textos personalizados no coincide con la cantidad del producto', [
                    'custom_text_count' => count($validated['custom_text']),
                    'quantity' => $orderItem->quantity
                ]);
                return response()->json([
                    'message' => 'La cantidad de textos personalizados debe coincidir con la cantidad del producto.'
                ], 422);
            }
        }*/

        $orderItem->custom_text = $validated['custom_text'];
        $orderItem->save();

        Log::info('Textos personalizados actualizados correctamente', [
            'custom_text' => $orderItem->custom_text,
            'quantity' => $orderItem->quantity
        ]);

        return response()->json([
            'message' => 'Textos personalizados actualizados correctamente',
            'custom_text' => $orderItem->custom_text,
            'quantity' => $orderItem->quantity
        ], 200);
    }
}
