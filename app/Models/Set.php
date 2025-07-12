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
        'id_discount'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_set', 'id_set', 'id_product');
    }
}
