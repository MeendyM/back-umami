<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//seria product order
class OrderItem extends Model
{
    protected $primaryKey = 'id_order_item';

    protected $fillable = ['id_order', 'id_product', 'quantity', 'subtotal', 'is_customized', 'custom_text', 'id_user', 'set_id'];
    //se creara la orden hasta que se confirme en el carrito id order es null (controlador que crea la orden y coloca el id order de los que estan en el carrito)

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'id_order_item');
    }

    public function set()
    {
        return $this->belongsTo(Set::class, 'set_id', 'id_set');
    }

    /**
     * Verifica si este item proviene de un set
     */
    public function isFromSet()
    {
        return !is_null($this->set_id);
    }

    protected $casts = [
        'custom_text' => 'array',
    ];
}
