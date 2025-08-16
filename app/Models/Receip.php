<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receip extends Model
{
    protected $primaryKey = 'id_receip';


    protected $fillable = [
        'id_order', 'id_user', 'amount', 'id_transaction', 'url_img', 'status', 'payment_type'
    ];


    protected $casts = [
        'status' => \App\Enums\ReceipStatus::class,
        'payment_type' => \App\Enums\ReceipPaymentType::class,
    ];


    protected $attributes = [
        'status' => 'sent',
        'payment_type' => 'cash',
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
