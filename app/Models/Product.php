<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'id_product';

    protected $fillable = ['name', 'description', 'price', 'url_imagen', 'id_supplier', 'id_category', 'is_customized', 'only_in_set'];

    //agrear el campo para el arreglo de urls de imagenes y el identificador de la categoría

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id_product');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id_product');
    }

    /**
     * Verifica si este producto solo se puede comprar en sets
     */
    public function isOnlyInSet()
    {
        return $this->only_in_set;
    }

    protected $casts = [
        'url_imagen' => 'array',
    ];
}
