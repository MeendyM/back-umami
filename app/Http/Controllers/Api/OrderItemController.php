<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;

class OrderItemController extends Controller
{
    //recibe el id del producto y crea la orden si no existe con ese usuario
    public function addProduct(Request $request)
    {
        $userId = $request->user()->id_user;

        $request->validate([
            'id_product' => 'required|integer|exists:products,id_product',
        ]);

        $order = Order::where('id_user', $userId)->first();

        //Si no existe la orden, se crea una nueva y se agrega el item a esa orden en la tabla order_items
        if (!$order) {

            //Crear nueva orden
            Order::create([
                'id_user' => $userId,
                'id_status_order' => 1,
            ]);

            $order = Order::where('id_user', $userId)->first();

            //Crear nuevo item en la orden
            OrderItem::create([
                'id_order' => $order->id_order,
                'id_product' => $request->id_product,
                'quantity' => 1,
                'subtotal' => Product::find($request->id_product)->price,
            ]);

            //actualizar el total de la orden
            $order->total = Product::find($request->id_product)->price;
            $order->save();

            return response()->json([
                'message' => 'Producto agregado a la orden',
                'order_id' => $order->id_order,
                'total' => $order->total,
            ], 201);
        } else {
            //Si la orden ya existe, se agrega el item a la orden existente

            //verificar si el item ya esta en la orden
            $orderItem = OrderItem::where('id_order', $order->id_order)
                ->where('id_product', $request->id_product)
                ->first();

            if (!$orderItem) {

                //Si el item no existe, se agrega a la orden
                $product = Product::find($request->id_product);
                $price = $product->price;

                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_product' => $request->id_product,
                    'quantity' => 1,
                    'subtotal' => $price,
                ]);

                //Actualizar el total de la orden
                $order->total += $price;
                $order->save();

                return response()->json([
                    'message' => 'Producto agregado a la orden',
                    'order_id' => $order->id_order,
                    'total' => $order->total,
                ], 201);
            } else {
                //Si el item ya existe, se actualiza la cantidad y el subtotal
                $orderItem->quantity += 1;
                $orderItem->subtotal += Product::find($request->id_product)->price;
                $orderItem->save();

                //Actualizar el total de la orden
                $order->total += Product::find($request->id_product)->price;
                $order->save();

                return response()->json([
                    'message' => 'Producto actualizado en la orden',
                    'order_id' => $order->id_order,
                    'total' => $order->total,
                ], 200);
            }
        }
    }
}
