<?php

namespace App\Enums;

/**
 * Tipo de OrderItem en el carrito/orden
 * - product: item unitario de producto
 * - set: item contenedor de un set (padre)
 */
enum OrderItemType: string
{
    case PRODUCT = 'product';
    case SET = 'set';

    public static function labels(): array
    {
        return [
            self::PRODUCT->value => 'Producto',
            self::SET->value => 'Set',
        ];
    }
}
