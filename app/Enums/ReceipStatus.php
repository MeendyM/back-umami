<?php

namespace App\Enums;

enum ReceipStatus: string

{
    case APPROVED = 'approved';
    case REVIEW = 'review';
    case SENT = 'sent';
    case RESENT = 'resent';

    public static function labels(): array
    {
        return [
            self::APPROVED->value => 'Aprobado',
            self::REVIEW->value => 'Revisar',
            self::SENT->value => 'Enviado',
            self::RESENT->value => 'Reenviado',
        ];
    }
}
