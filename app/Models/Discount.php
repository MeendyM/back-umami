<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_institution', //Llave foranea a la tabla de insitutciones 
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'expires_at'
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'id_institution');
    }
}
