<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_discount', //Llave foranea a la tabla de descuentos 
        'id_order', //Llave foranea a la tabla de ordenes 
        'id_user', //Llave foranea a la tabla de usuarios 
        'use_at',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'id_discount');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
}
