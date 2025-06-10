<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'id_product';

    protected $fillable = ['name', 'description', 'price', 'url_imagen', 'id_supplier', 'category', 'is_customized'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_product');
    }
}
