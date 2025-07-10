<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_user',
        'id_discount',
        'status',
        'total',
        'payment_type',
        'discount_amount',
        'final_total',//es el campo que se usa para calculos aunque el total sea el mismo
    ];

    // Relación: Orden pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relación: Orden puede tener un descuento
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'id_discount', 'id_discount');
    }

     public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_order');
    }

    public function receips()
    {
        return $this->hasMany(Receip::class, 'id_order');
    }

    public function discountUses()
    {
        return $this->hasMany(DiscountUse::class, 'id_order');
    }
}
