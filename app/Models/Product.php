<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Especificamos los campos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'description',
        'price',
        'url_imagen',
        'id_supplier',
        'id_category',
    ];

    // Definimos las relaciones con los modelos Supplier y Category
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }
}
