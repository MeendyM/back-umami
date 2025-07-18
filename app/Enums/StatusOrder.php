<?php

namespace App\Enums;

enum StatusOrder: string
{
    case REQUESTED = 'requested';
    case PAYING = 'paying';
    case UNDER_REVIEW = 'under_review';
    case PAID = 'paid';

    public static function labels(): array
    {
        return [
            self::REQUESTED->value => 'Solicitado',
            self::PAYING->value => 'Pagando',
            self::UNDER_REVIEW->value => 'En revisión',
            self::PAID->value => 'Pagado',
        ];
    }
}
