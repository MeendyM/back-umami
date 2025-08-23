<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;

    protected $table = 'sets';
    protected $primaryKey = 'id_set';

    protected $fillable = [
        'name',
        'description',
        'url_image',
        'id_discount',
        'price',
        'only_in_set',
        'id_supplier',
        'is_deleted',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_set', 'id_set', 'id_product');
    }

    /**
     * Proveedor principal del set (opcional)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_set', 'id_set');
    }
}
