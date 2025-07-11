<?php

namespace App\Enums;

enum TypeDiscount: string
{
    case FROM_400 = 'from_400';
    case FROM_1500 = 'from_1500';
    case FROM_2500 = 'from_2500';
    case FROM_400_AGAIN = 'from_4000';
    case SET = 'set';//Descuento para los sets
    case PRODUCT = 'product'; // Descuento para productos individuales

    public static function labels(): array
    {
        return [
            self::FROM_400->value => 'Desde $400',
            self::FROM_1500->value => 'Desde $1500',
            self::FROM_2500->value => 'Desde $2500',
            self::FROM_400_AGAIN->value => 'Desde $4000',
            self::SET->value => 'Set de productos',
        ];
    }
}
