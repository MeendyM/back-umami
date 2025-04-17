<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [1, 2, 3]; // IDs de órdenes
        $productIds = [1, 2, 3, 4, 5]; // IDs de productos

        foreach ($orders as $orderId) {

            $total = 0;

            $itemsCount = rand(1, 3); // Cantidad aleatoria de productos por orden
            $selectedProducts = collect($productIds)->shuffle()->take($itemsCount); // Seleccionar productos aleatorios

            foreach ($selectedProducts as $productId) {

                $product = Product::find($productId);

                if ($product) {

                    $quantity = rand(1, 5);
                    $subtotal = $product->price * $quantity;

                    OrderItem::create([
                        'id_order' => $orderId,
                        'id_product' => $productId,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal
                    ]);

                    $total += $subtotal;
                }
            }

            // Actualizar el total de la orden
            $order = Order::find($orderId);
            if ($order) {
                $order->total = $total;
                $order->final_total = $total; // o aplica descuentos si los tienes
                $order->save();
            }
        }
    }
}
