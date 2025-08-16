<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class WebControllers extends Controller
{
    public function products()
    {
        return view('products.index');
    }

    public function collections()
    {
        return view('collections.index');
    }

    public function discounts()
    {
        return view('discounts.index');
    }

    public function orders()
    {
        return view('orders.index');
    }
    public function orderItems()
    {
        return view('orders-items.index');
    }
    public function notifications()
    {
        return view('notifications.index');
    }
}
