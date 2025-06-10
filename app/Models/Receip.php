<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receip extends Model
{
    protected $primaryKey = 'id_receip';

    protected $fillable = ['id_order', 'id_user', 'amount', 'id_transaction', 'url_img'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
