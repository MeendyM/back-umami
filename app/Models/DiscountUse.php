<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountUse extends Model
{
    protected $primaryKey = 'id_discount_use';

    protected $fillable = ['id_discount', 'id_order', 'id_user', 'use_at'];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'id_discount');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
