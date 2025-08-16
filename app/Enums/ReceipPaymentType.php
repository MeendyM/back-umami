<?php

namespace App\Enums;

enum ReceipPaymentType: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';

    public static function labels(): array
    {
        return [
            self::CASH->value => 'Efectivo',
            self::TRANSFER->value => 'Transferencia',
        ];
    }
}
