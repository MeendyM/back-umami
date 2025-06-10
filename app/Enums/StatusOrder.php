<?php

namespace App\Enums;

enum StatusOrder: string
{
    case REQUESTED = 'requested';
    case PAYING = 'paying';
    case PAID = 'paid';

    public static function labels(): array
    {
        return [
            self::REQUESTED->value => 'Solicitado',
            self::PAYING->value => 'Pagando',
            self::PAID->value => 'Pagado',
        ];
    }
}
