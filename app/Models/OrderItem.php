<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_order_item';

    protected $fillable = [
        'id_order', //Llave foranea a la tabla de ordenes 
        'id_product', //Llave foranea a la tabla de productos 
        'quantity',
        'subtotal',
        'is_customized',
        'custom_text'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
