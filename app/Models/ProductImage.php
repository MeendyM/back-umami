<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id_product_image';
    protected $fillable = [
        'product_id',
        'url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
