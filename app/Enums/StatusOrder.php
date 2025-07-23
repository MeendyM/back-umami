<?php

namespace App\Enums;

enum StatusOrder: string
{
    case REQUESTED = 'requested';
    case PAYING = 'paying';
    case UNDER_REVIEW = 'under_review';
    case REVIEW = 'review';
    case PAID = 'paid';
    case DELIVERED = 'delivered';

    public static function labels(): array
    {
        return [
            self::REQUESTED->value => 'Solicitado',
            self::PAYING->value => 'Pagando',
            self::UNDER_REVIEW->value => 'En revisión',
            self::REVIEW->value => 'Revisar',
            self::PAID->value => 'Pagado',
            self::DELIVERED->value => 'Entregado',
        ];
    }
}
