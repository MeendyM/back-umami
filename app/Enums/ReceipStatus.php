<?php

namespace App\Enums;

enum ReceipStatus: string
{
    case APROBADO = 'aprobado';
    case REVISAR = 'revisar';
    case ENVIADO = 'enviado';
    case REENVIADO = 'reenviado';

    public static function labels(): array
    {
        return [
            self::APROBADO->value => 'Aprobado',
            self::REVISAR->value => 'Revisar',
            self::ENVIADO->value => 'Enviado',
            self::REENVIADO->value => 'Reenviado',
        ];
    }
}
