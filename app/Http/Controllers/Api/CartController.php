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
use App\Models\Set; // Import Set para manejo de sets
use Illuminate\Support\Facades\DB; // DB para transacciones

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

        // Si viene id_set, procesar flujo de set y salir sin tocar la lógica existente de productos
        if ($request->filled('id_set')) {
            return $this->addSetToCart($request, $userId);
        }

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

        Log::info('Intentando remover item del carrito', [
            'user_id' => $userId,
            'request' => $request->all()
        ]);

         // Buscar el item en 'order_items' del usuario
       /* $orderItem = OrderItem::where('id_user', $userId)
            ->where('id_product', $request->id_product)
            ->first();*/


        // Buscar el item en 'order_items' del usuario por id_order_item
        $orderItem = OrderItem::where('id_user', $userId)
            ->where('id_order_item', $request->id_order_item)
            ->first();

        Log::info('Item encontrado para remover', [
            'orderItem' => $orderItem
        ]);

        if (!$orderItem) {
            Log::warning('Item no encontrado en el carrito para remover', [
                'user_id' => $userId,
                'id_order_item' => $request->id_order_item
            ]);
            return response()->json(['message' => 'Item no encontrado en el carrito'], 404);
        }

        // Eliminar el item del carrito y su relación en Cart siempre
        Cart::where('id_order_item', $request->id_order_item)->delete();
        $orderItem->delete();

        Log::info('Item y relación en carrito eliminados', [
            'id_order_item' => $request->id_order_item
        ]);

        return response()->json([
            'message' => 'Item eliminado del carrito'
        ], 200);
    }

    /**
     * Elimina un set (OrderItem padre) y todos sus OrderItems hijos (productos del set)
     */
    public function removeSet(Request $request)
    {
        $userId = $request->user()->id_user;

        $validated = $request->validate([
            'id_set_item' => 'required|integer', // id_order_item del OrderItem padre (set)
        ]);

        $parent = OrderItem::where('id_user', $userId)
            ->where('id_order_item', $validated['id_set_item'])
            ->whereNull('id_order') // solo en carrito (no asignado a orden todavía)
            ->whereNotNull('id_set') // debe ser un item de set
            ->first();

        if (!$parent) {
            Log::warning('Set a eliminar no encontrado o no pertenece al usuario', [
                'user_id' => $userId,
                'id_set_item' => $validated['id_set_item']
            ]);
            return response()->json(['message' => 'Set no encontrado en el carrito'], 404);
        }

        // Reunir IDs de hijos antes de borrar para limpiar Cart
        $childIds = $parent->children()->pluck('id_order_item')->toArray();

        DB::beginTransaction();
        try {
            // Eliminar relaciones en Cart de hijos y padre
            if (!empty($childIds)) {
                Cart::whereIn('id_order_item', $childIds)->delete();
            }
            Cart::where('id_order_item', $parent->id_order_item)->delete();

            // Borrar el padre (por FK con cascadeOnDelete se eliminan hijos)
            $parent->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al eliminar set del carrito', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'No se pudo eliminar el set'], 500);
        }

        return response()->json([
            'message' => 'Set eliminado del carrito',
            'deleted_parent_id' => $validated['id_set_item'],
            'deleted_children_ids' => $childIds,
        ], 200);
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
            'custom_key' => 'array'
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
      /*  if (count($validated['custom_key']) !== $cantidadARetirar) {
            Log::warning('Cantidad de customizaciones a eliminar no coincide con la cantidad a retirar', [
                'custom_key_count' => count($validated['custom_key']),
                'cantidad_a_retirar' => $cantidadARetirar
            ]);
            return response()->json([
                'message' => 'La cantidad de customizaciones a eliminar debe coincidir con la cantidad que deseas retirar'
            ], 422);
        }
*/
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
            'final_total' => $total,
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

    public function addMultipleItems(Request $request)
    {
        $userId = $request->user()->id_user;

        Log::info('Intentando agregar múltiples items al carrito', [
            'user_id' => $userId,
            'request' => $request->all()
        ]);

        $request->validate([
            'items' => 'required|array',
            'items.*.id_product' => 'required|integer|exists:products,id_product',
            'items.*.customs' => 'nullable|array',
            'items.*.customs.*' => 'string',
            'items.*.quantity' => 'nullable|integer|min:1'
        ]);

        Log::info('Validación exitosa para agregar múltiples items');

        $addedItems = [];

        foreach ($request->input('items') as $itemData) {
            Log::info('Procesando item', ['itemData' => $itemData]);

            $product = Product::find($itemData['id_product']);
            $quantity = $itemData['quantity'] ?? 1; // Default: 1
            $customizations = $itemData['customs'] ?? [];

            // Buscar si el producto ya está en el carrito del usuario sin orden asignada
            $existingItem = OrderItem::where('id_user', $userId)
                ->where('id_product', $product->id_product)
                ->whereNull('id_order')
                ->first();

            Log::info('Buscando item existente', ['existingItem' => $existingItem]);

            // Determinar si el producto es personalizado
            $isCustomized = (bool)($product->is_customized ?? false);

            if ($existingItem) {
                Log::info('Item existente encontrado, actualizando', ['existingItem' => $existingItem]);
                // Actualizar item existente
                $existingItem->quantity += $quantity;
                $existingItem->subtotal += $product->price * $quantity;

                $new = $customizations;
                $current = $existingItem->custom_text;

                // Unir los textos personalizados correctamente
                $resultado = array_merge($current ?? [], $new ?? []);

                $existingItem->custom_text = $resultado;
                $existingItem->is_customized = $isCustomized;

                $existingItem->save();

                Log::info('Item existente actualizado', ['item' => $existingItem]);

                $addedItems[] = [
                    'message' => 'Producto actualizado en el carrito',
                    'item' => $existingItem
                ];

            } else {
                Log::info('Item no encontrado, creando nuevo item');
                // Crear nuevo item en carrito
                $newItem = OrderItem::create([
                    'id_user' => $userId,
                    'id_product' => $product->id_product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                    'custom_text' => $customizations,
                    'is_customized' => $isCustomized,
                ]);

                Cart::create([
                    'id_user' => $userId,
                    'id_order_item' => $newItem->id_order_item,
                ]);

                Log::info('Nuevo item creado', ['newItem' => $newItem]);

                $addedItems[] = [
                    'message' => 'Producto agregado al carrito',
                    'item' => $newItem
                ];
            }
        }

        Log::info('Proceso de agregar múltiples items completado', ['addedItemsCount' => count($addedItems)]);

        return response()->json(['results' => $addedItems], 200);
    }

    /**
     * Agrega un set al carrito creando:
     * - Un OrderItem padre con id_set (subtotal = precio del set si existe)
     * - OrderItems por producto con only_in_set=true y parent asignado
     */
    private function addSetToCart(Request $request, int $userId)
    {
        // Validación específica para sets
        $validated = $request->validate([
            'id_set' => 'required|integer|exists:sets,id_set',
            //'quantity' => 'nullable|integer|min:1',
            'products' => 'required|array|min:1',
            'products.*.id_product' => 'required|integer|exists:products,id_product',
            'products.*.customs' => 'nullable|array',
            'products.*.customs.*' => 'string',
        ]);

        // Definir cantidad por defecto a 1 (si se envía, se respeta)
        $quantity = (int) $request->input('quantity', 1);

        $set = Set::find($validated['id_set']);

        if (!$set) {
            Log::warning('Set no encontrado', ['id_set' => $validated['id_set']]);
            return response()->json(['message' => 'Set no encontrado'], 404);
        }

        // Calcular suma de precios de productos si el set no tiene precio definido
        $sumProducts = 0;
        foreach ($validated['products'] as $p) {
            $product = Product::find($p['id_product']);
            if (!$product) {
                Log::warning('Producto no encontrado al agregar set', ['id_product' => $p['id_product']]);
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }
            $sumProducts += ($product->price ?? 0);
        }

        $unitSetPrice = $set->price ?? $sumProducts; // precio unitario del set

        $result = DB::transaction(function () use ($userId, $set, $quantity, $validated, $unitSetPrice, $sumProducts) {
            // Crear OrderItem para el set (ítem valorado)
            $setItem = OrderItem::create([
                'id_user' => $userId,
                'id_set' => $set->id_set,
                'id_product' => null,
                'quantity' => $quantity,
                'subtotal' => ($set->price !== null ? ($set->price * $quantity) : 0), // se ajusta si no hay price
                'custom_text' => [],
                'is_customized' => false,
                'type_order' => 'set',
            ]);

            // Ajustar subtotal si el set no tiene price definido
            if ($set->price === null) {
                $setItem->subtotal = $sumProducts * $quantity;
                $setItem->save();
            }

            Cart::create([
                'id_user' => $userId,
                'id_order_item' => $setItem->id_order_item,
            ]);

            Log::info('OrderItem de set creado', [
                'set_item' => $setItem
            ]);

            // Crear OrderItems por producto del set, marcando only_in_set=true, parent asignado y subtotal=0
            $productItems = [];
            foreach ($validated['products'] as $p) {
                $product = Product::find($p['id_product']);
                if (!$product) {
                    continue; // validado arriba
                }

                $customs = $p['customs'] ?? [];
                $item = OrderItem::create([
                    'id_user' => $userId,
                    'id_set' => $set->id_set,
                    'id_parent_order_item' => $setItem->id_order_item,
                    'id_product' => $product->id_product,
                    'quantity' => $quantity,
                    'subtotal' => 0, // el precio lo lleva el ítem del set
                    'custom_text' => $customs,
                    'is_customized' => (bool)($product->is_customized ?? false),
                    'only_in_set' => true,
                    // type_order se mantiene por defecto 'product'
                ]);

                Cart::create([
                    'id_user' => $userId,
                    'id_order_item' => $item->id_order_item,
                ]);

                $productItems[] = $item;
            }

            return [$setItem, $productItems];
        });

        [$setItem, $productItems] = $result;

        return response()->json([
            'message' => 'Set agregado al carrito',
            'set_item' => $setItem,
            'product_items' => $productItems,
        ], 201);
    }
}
