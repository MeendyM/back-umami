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

        // Obtener los items del carrito del usuario, incluyendo producto y set si aplica
        $cartItems = Cart::where('id_user', $userId)
            ->with(['orderItem.product', 'orderItem.set'])
            ->get();

       

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

        // Buscar si el producto ya está en el carrito del usuario, SOLO items standalone (no pertenecen a un set)
        $existingItem = OrderItem::where('id_user', $userId)
            ->where('id_product', $product->id_product)
            ->whereNull('id_order')
            ->whereNull('id_set') // asegurar que no pertenezca a un set
            ->where(function ($q) { // y que no esté marcado como only_in_set
                $q->whereNull('only_in_set')->orWhere('only_in_set', false);
            })
            ->first();

        Log::info('Item existente en carrito (standalone)', [
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

                Log::info('Actualizando item existente (standalone, sumando 1)', [
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

                Log::info('Actualizando item existente (standalone, sumando cantidad personalizada)', [
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

        // Si no existe item standalone, crear uno nuevo (no asociado a set)
        $newItem = OrderItem::create([
            'id_user' => $userId,
            'id_product' => $product->id_product,
            'id_set' => null,
            'only_in_set' => false,
            'quantity' => $quantity,
            'subtotal' => $product->price * $quantity,
            'custom_text' => $customizations,
            'is_customized' => $isCustomized,
        ]);

        Log::info('Creando nuevo item en carrito (standalone)', [
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

        // Si el item es un set, podremos eliminar parcial o totalmente
        $isSetItem = (($orderItem->type_order ?? null) === 'set') || ($orderItem->id_set && $orderItem->id_product === null);
        
        Log::info('Verificando tipo de item', [
            'is_set_item' => $isSetItem,
            'type_order' => $orderItem->type_order ?? null,
            'id_set' => $orderItem->id_set,
            'id_product' => $orderItem->id_product,
        ]);
        
        if ($isSetItem) {
            $requestedRemoveQty = (int)($request->input('cantidad_a_retirar', 0));
            $customRemovals = $request->input('custom_removals', []); // [{ id_product: int, indexes: int[] }]

            Log::info('Procesando remoción de set', [
                'requested_remove_qty' => $requestedRemoveQty,
                'current_set_quantity' => $orderItem->quantity,
                'custom_removals' => $customRemovals,
            ]);

            // Remoción parcial si se pide cantidad menor al total del set
            if ($requestedRemoveQty > 0 && $requestedRemoveQty < (int)$orderItem->quantity) {
                $removeQty = $requestedRemoveQty;

                DB::transaction(function () use ($orderItem, $userId, $removeQty, $customRemovals) {
                    // 1) Hijos por relación de padre
                    $childrenByParent = OrderItem::where('id_user', $userId)
                        ->whereNull('id_order')
                        ->where('id_parent_order_item', $orderItem->id_order_item)
                        ->get();

                    if ($childrenByParent->isNotEmpty()) {
                        foreach ($childrenByParent as $child) {
                            $newQty = max(0, (int)$child->quantity - $removeQty);

                            // Ajustar custom_text
                            $texts = $child->custom_text;
                            if (is_array($texts)) {
                                // Buscar remociones específicas por producto
                                $productRem = collect($customRemovals)->firstWhere('id_product', (int)$child->id_product);
                                if ($productRem && is_array($productRem['indexes'] ?? null)) {
                                    $indexes = array_values(array_unique(array_map('intval', $productRem['indexes'])));
                                    rsort($indexes); // eliminar de mayor a menor
                                    foreach ($indexes as $idx) {
                                        if ($idx >= 0 && $idx < count($texts)) {
                                            array_splice($texts, $idx, 1);
                                        }
                                    }
                                    $child->custom_text = array_values($texts);
                                } else {
                                    // Por defecto, eliminar del final tantos como removeQty
                                    $removeCount = min($removeQty, count($texts));
                                    if ($removeCount > 0) {
                                        $child->custom_text = array_slice($texts, 0, count($texts) - $removeCount);
                                    }
                                }
                            }

                            if ($newQty <= 0) {
                                Cart::where('id_order_item', $child->id_order_item)->delete();
                                $child->delete();
                            } else {
                                $child->quantity = $newQty;
                                $child->save();
                            }
                        }
                    } else {
                        // 2) Hijos acumulados por id_set + id_product
                        $aggChildren = OrderItem::where('id_user', $userId)
                            ->whereNull('id_order')
                            ->where('id_set', $orderItem->id_set)
                            ->whereNotNull('id_product')
                            ->where('only_in_set', true)
                            ->get();

                        foreach ($aggChildren as $child) {
                            $newQty = max(0, (int)$child->quantity - $removeQty);

                            // Ajustar custom_text
                            $texts = $child->custom_text;
                            if (is_array($texts)) {
                                $productRem = collect($customRemovals)->firstWhere('id_product', (int)$child->id_product);
                                if ($productRem && is_array($productRem['indexes'] ?? null) && count($productRem['indexes']) === $removeQty) {
                                    $indexes = array_values(array_unique(array_map('intval', $productRem['indexes'])));
                                    rsort($indexes);
                                    foreach ($indexes as $idx) {
                                        if ($idx >= 0 && $idx < count($texts)) {
                                            array_splice($texts, $idx, 1);
                                        }
                                    }
                                    $child->custom_text = array_values($texts);
                                } else {
                                    $removeCount = min($removeQty, count($texts));
                                    if ($removeCount > 0) {
                                        $child->custom_text = array_slice($texts, 0, count($texts) - $removeCount);
                                    }
                                }
                            }

                            if ($newQty <= 0) {
                                Cart::where('id_order_item', $child->id_order_item)->delete();
                                $child->delete();
                            } else {
                                $child->quantity = $newQty;
                                // subtotal permanece 0 (precio en set)
                                $child->save();
                            }
                        }
                    }

                    // 3) Actualizar set padre: restar quantity y subtotal proporcional
                    $currentQty = (int)$orderItem->quantity;
                    $unitPrice = $currentQty > 0 ? ((float)$orderItem->subtotal / $currentQty) : 0.0;
                    $orderItem->quantity = $currentQty - $removeQty;
                    $orderItem->subtotal = max(0, (float)$orderItem->subtotal - ($unitPrice * $removeQty));
                    $orderItem->save();

                    // Asegurar relación en carrito si sigue existiendo
                    if ($orderItem->quantity > 0) {
                        if (!Cart::where('id_user', $userId)->where('id_order_item', $orderItem->id_order_item)->exists()) {
                            Cart::create([
                                'id_user' => $userId,
                                'id_order_item' => $orderItem->id_order_item,
                            ]);
                        }
                    }
                });

                return response()->json([
                    'message' => 'Cantidad del set actualizada y items relacionados ajustados',
                    'remaining_quantity' => $orderItem->fresh()->quantity,
                ], 200);
            }

            // Remoción total (caso anterior): elimina todos los items relacionados y luego el set
            DB::transaction(function () use ($orderItem, $userId) {
                $removeQty = (int) ($orderItem->quantity ?? 1);

                // 1) Intentar encontrar hijos ligados explícitamente por id_parent_order_item
                $childrenByParent = OrderItem::where('id_user', $userId)
                    ->whereNull('id_order')
                    ->where('id_parent_order_item', $orderItem->id_order_item)
                    ->get();

                if ($childrenByParent->isNotEmpty()) {
                    foreach ($childrenByParent as $child) {
                        Cart::where('id_order_item', $child->id_order_item)->delete();
                        $child->delete();
                    }
                    Log::info('Hijos del set eliminados por relación de padre', [
                        'parent_id' => $orderItem->id_order_item,
                        'count' => $childrenByParent->count()
                    ]);
                } else {
                    // 2) Modo acumulado: restar cantidad a hijos agrupados por id_set + id_product
                    $aggChildren = OrderItem::where('id_user', $userId)
                        ->whereNull('id_order')
                        ->where('id_set', $orderItem->id_set)
                        ->whereNotNull('id_product')
                        ->where('only_in_set', true)
                        ->get();

                    foreach ($aggChildren as $child) {
                        $newQty = max(0, (int)$child->quantity - $removeQty);
                        if ($newQty <= 0) {
                            Cart::where('id_order_item', $child->id_order_item)->delete();
                            $child->delete();
                            continue;
                        }

                        // Ajustar custom_text removiendo últimos N elementos si es array
                        $texts = $child->custom_text;
                        if (is_array($texts)) {
                            $removeCount = min($removeQty, count($texts));
                            if ($removeCount > 0) {
                                $child->custom_text = array_slice($texts, 0, count($texts) - $removeCount);
                            }
                        }

                        $child->quantity = $newQty;
                        // subtotal permanece 0 (el precio está en el set)
                        $child->save();
                    }

                    Log::info('Hijos del set actualizados en modo acumulado', [
                        'id_set' => $orderItem->id_set,
                        'count' => $aggChildren->count()
                    ]);
                }

                // 3) Remover el set padre del carrito y de order_items
                Cart::where('id_order_item', $orderItem->id_order_item)->delete();
                $orderItem->delete();

                Log::info('Set y relaciones eliminadas', [
                    'deleted_set_id' => $orderItem->id_order_item
                ]);
            });

            return response()->json([
                'message' => 'Set y sus items relacionados eliminados del carrito'
            ], 200);
        }

        // Caso normal: eliminar solo ese item (producto individual)
        Log::info('Procesando remoción de producto individual', [
            'id_order_item' => $request->id_order_item,
            'is_set_item' => false,
            'id_product' => $orderItem->id_product,
            'id_set' => $orderItem->id_set,
            'only_in_set' => $orderItem->only_in_set,
            'quantity' => $orderItem->quantity,
            'subtotal' => $orderItem->subtotal,
        ]);

        // Verificar si es remoción parcial para producto individual
        $requestedRemoveQty = (int)($request->input('cantidad_a_retirar', 0));
        if ($requestedRemoveQty > 0 && $requestedRemoveQty < (int)$orderItem->quantity) {
            Log::info('Remoción parcial de producto individual', [
                'requested_remove_qty' => $requestedRemoveQty,
                'current_quantity' => $orderItem->quantity,
            ]);

            // Restar cantidad y recalcular subtotal
            $product = Product::find($orderItem->id_product);
            if (!$product) {
                Log::warning('Producto no encontrado para remoción parcial', [
                    'id_product' => $orderItem->id_product
                ]);
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            $orderItem->quantity -= $requestedRemoveQty;
            $orderItem->subtotal = $orderItem->quantity * $product->price;

            // Ajustar custom_text si es array
            $texts = $orderItem->custom_text;
            if (is_array($texts)) {
                $removeCount = min($requestedRemoveQty, count($texts));
                if ($removeCount > 0) {
                    $orderItem->custom_text = array_slice($texts, 0, count($texts) - $removeCount);
                }
            }

            $orderItem->save();

            Log::info('Producto individual actualizado', [
                'new_quantity' => $orderItem->quantity,
                'new_subtotal' => $orderItem->subtotal,
                'new_custom_text' => $orderItem->custom_text,
            ]);

            return response()->json([
                'message' => 'Cantidad del producto actualizada',
                'remaining_quantity' => $orderItem->quantity,
                'subtotal' => $orderItem->subtotal,
            ], 200);
        }

        // Remoción total del producto individual
        Log::info('Eliminando producto individual completamente', [
            'id_order_item' => $request->id_order_item,
        ]);

        Cart::where('id_order_item', $request->id_order_item)->delete();
        $orderItem->delete();

        Log::info('Item y relación en carrito eliminados', [
            'id_order_item' => $request->id_order_item
        ]);

        return response()->json([
            'message' => 'Item eliminado del carrito'
        ], 200);
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

    public function clear(Request $request)
    {
        $userId = $request->user()->id_user;

        // Eliminar todos los items del carrito del usuario
        OrderItem::where('id_user', $userId)->delete();
        Cart::where('id_user', $userId)->delete();


        return response()->json(['message' => 'Carrito vaciado correctamente'], 200);
    }

    /**
     * Agrega un set al carrito creando/actualizando:
     * - Un OrderItem padre con id_set (subtotal = precio del set si existe o suma de productos)
     * - OrderItems por producto con only_in_set=true, acumulando quantity/custom_text por id_set+id_product
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

        $result = DB::transaction(function () use ($userId, $set, $quantity, $validated, $unitSetPrice) {
            // Buscar si ya existe un OrderItem del set para acumular
            $existingSetItem = OrderItem::where('id_user', $userId)
                ->whereNull('id_order')
                ->where('id_set', $set->id_set)
                ->whereNull('id_product')
                ->where('type_order', 'set')
                ->first();

            if ($existingSetItem) {
                $existingSetItem->quantity += $quantity;
                $existingSetItem->subtotal += ($unitSetPrice * $quantity);
                $existingSetItem->save();

                // Asegurar relación en carrito
                if (!Cart::where('id_user', $userId)->where('id_order_item', $existingSetItem->id_order_item)->exists()) {
                    Cart::create([
                        'id_user' => $userId,
                        'id_order_item' => $existingSetItem->id_order_item,
                    ]);
                }

                $setItem = $existingSetItem;
                Log::info('OrderItem de set actualizado (acumulado)', [
                    'set_item' => $setItem
                ]);
            } else {
                // Crear OrderItem para el set (ítem valorado)
                $setItem = OrderItem::create([
                    'id_user' => $userId,
                    'id_set' => $set->id_set,
                    'id_product' => null,
                    'quantity' => $quantity,
                    'subtotal' => ($unitSetPrice * $quantity),
                    'custom_text' => [],
                    'is_customized' => false,
                    'type_order' => 'set',
                    'only_in_set' => $set->only_in_set ?? false, // solo si el set lo tiene definido
                ]);

                Cart::create([
                    'id_user' => $userId,
                    'id_order_item' => $setItem->id_order_item,
                ]);

                Log::info('OrderItem de set creado', [
                    'set_item' => $setItem
                ]);
            }

            // Crear/actualizar OrderItems por producto del set, marcando only_in_set=true
            $productItems = [];
            foreach ($validated['products'] as $p) {
                $product = Product::find($p['id_product']);
                if (!$product) {
                    continue; // validado arriba
                }

                $customs = $p['customs'] ?? [];

                // Buscar si ya existe un item de este producto para este set sin id_order
                $existingChild = OrderItem::where('id_user', $userId)
                    ->whereNull('id_order')
                    ->where('id_set', $set->id_set)
                    ->where('id_product', $product->id_product)
                    ->where('only_in_set', true)
                    ->first();

                if ($existingChild) {
                    $existingChild->quantity += $quantity;
                    $currentCustoms = $existingChild->custom_text ?? [];
                    $existingChild->custom_text = array_values(array_merge($currentCustoms, $customs ?? []));
                    $existingChild->is_customized = (bool)($product->is_customized ?? false);
                    // Mantener subtotal en 0; el precio lo lleva el ítem del set
                    $existingChild->save();

                    // Asegurar que exista relación en Cart
                    $hasCart = Cart::where('id_user', $userId)
                        ->where('id_order_item', $existingChild->id_order_item)
                        ->exists();
                    if (!$hasCart) {
                        Cart::create([
                            'id_user' => $userId,
                            'id_order_item' => $existingChild->id_order_item,
                        ]);
                    }

                    $productItems[] = $existingChild;
                    Log::info('Item de producto en set actualizado', [
                        'id_product' => $product->id_product,
                        'item' => $existingChild,
                    ]);
                } else {
                    // Crear nuevo item de producto ligado al set, sin padre específico para permitir acumulación
                    $item = OrderItem::create([
                        'id_user' => $userId,
                        'id_set' => $set->id_set,
                        'id_parent_order_item' => null, // sin padre para permitir agrupar múltiples sets
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
                    Log::info('Item de producto en set creado', [
                        'id_product' => $product->id_product,
                        'item' => $item,
                    ]);
                }
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
