<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_discount';

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    // Relación: Un descuento puede estar en varios pedidos
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_discount', 'id_discount');
    }
}
