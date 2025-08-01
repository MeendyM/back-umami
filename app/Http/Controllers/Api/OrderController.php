<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    // Obtener todas las órdenes del usuario autenticado
    public function userOrders(Request $request)
    {
        $userId = $request->user()->id_user;

        $orders = Order::where('id_user', $userId)
            ->get();

        // Agregar label en español para status
        foreach ($orders as $order) {
            $order->status_label = \App\Enums\StatusOrder::labels()[$order->status] ?? $order->status;
        }

        return response()->json($orders, 200);
    }

    // Obtener una orden específica por id, solo si pertenece al usuario autenticado
    public function userOrderById(Request $request, $id_order)
    {
        $userId = $request->user()->id_user;
        $order = Order::where('id_order', $id_order)
            ->where('id_user', $userId)
            ->with([
                'orderItems.product:id_product,name,price'
            ])
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada o no pertenece al usuario'], 404);
        }

        // Agregar label en español para status
        $order->status_label = \App\Enums\StatusOrder::labels()[$order->status] ?? $order->status;

        return response()->json($order, 200);
    }
}
