<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_order';

    // Campos para asignar masivamente
    protected $fillable = [
        'id_user',
        'status',
        'total',
        'id_discount',
        'id_payment_type',
        'discount_amount',
        'final_total'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'id_discount');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'id_payment_type');
    }

    public function status()
    {
        return $this->belongsTo(StatusOrder::class, 'id_status_order');
    }
}
