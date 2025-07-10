<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSet extends Model
{
    use HasFactory;

    protected $table = 'product_set';
    protected $primaryKey = 'id_product_set';

    protected $fillable = [
        'set_id',
        'product_id',
    ];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
