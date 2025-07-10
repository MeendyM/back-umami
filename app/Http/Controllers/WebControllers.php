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
}
