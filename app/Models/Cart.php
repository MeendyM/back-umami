<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $primaryKey = 'id_cart';

    protected $fillable = ['id_user', 'id_order_item'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'id_order_item');
    }
}
