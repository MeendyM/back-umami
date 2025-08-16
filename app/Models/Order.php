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
        'order_code',
    ];

    /**
     * Boot method para generar order_code automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = self::generateUniqueOrderCode();
            }
        });
    }

    /**
     * Generar un código único de 5 caracteres alfanuméricos
     */
    public static function generateUniqueOrderCode(): string
    {
        do {
            // Generar código de 5 caracteres con letras y números
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }

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
