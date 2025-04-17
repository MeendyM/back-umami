<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::select('id_order', 'id_user', 'id_status_order', 'total', 'final_total', 'created_at')
            ->with([
                'user:id_user,name,email',
                'status:id_status_order,name'
            ])->get();
        return response()->json($orders, 200);
    }

    public function products($id_order)
    {
        $items = OrderItem::with(['product:id_product,name,price,url_imagen'])
            ->where('id_order', $id_order)
            ->get();

        return response()->json($items, 200);
    }
}
