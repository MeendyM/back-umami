<?php

namespace App\Enums;

enum PaymentType: string
{
    case SINGLE_PAYMENT = 'single_payment';
    case PARTIAL_PAYMENT = 'partial_payment';

    public static function labels(): array
    {
        return [
            self::SINGLE_PAYMENT->value => 'En una sola exhibición',
            self::PARTIAL_PAYMENT->value => 'Pago por partes',
        ];
    }

    //    'payment_type' => PaymentType::SINGLE_PAYMENT->value,

}
