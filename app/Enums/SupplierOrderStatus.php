<?php

namespace App\Enums;

/**
 * Estado del pedido al proveedor para un OrderItem
 * - not_ordered: no se ha pedido al proveedor
 * - ordered: ya se pidió al proveedor
 * - delivered: el proveedor ya entregó el ítem
 */
enum SupplierOrderStatus: string
{
    case NOT_ORDERED = 'not_ordered';
    case ORDERED = 'ordered';
    case DELIVERED = 'delivered';

    public static function labels(): array
    {
        return [
            self::NOT_ORDERED->value => 'Sin pedir',
            self::ORDERED->value => 'Pedido',
            self::DELIVERED->value => 'Entregado',
        ];
    }
}
