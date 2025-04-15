<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receip extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_order', //Llave foranea a la tabla de ordenes
        'id_user', //Llave foranea a la tabla de usuarios
        'amount',
        'id_transaction', //codigo de la operacion de la transferencia o deposito
        'url_img', //url de la imagen del recibo
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
