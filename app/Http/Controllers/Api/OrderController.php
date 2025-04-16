<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{

    //Create order
    public function store(Request $request)
    {
        //Añadir valdaciones porteriormente

        //ademas de añadir logica para evitar crear ordenes duplicadas

        $order = Order::create([
            'id_user' => $request->id_user,
            'status' => 'pending',
            'total' => 0, //revisar si se calculara automaticamente cuando se agregue el primero producto a la orden,
            'final_total' => 0 //Revisar si se calculara automaticamente cuando se agregue el primero producto a la orden y posteriormente ir actualizando

            //Descuento y tipo de pago se asignaran posteriormente al final cuando se confirme la orden

        ]);
    }
}
