<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case UNVERIFIED = 'UNVERIFIED';
    case PENDING = 'PENDING';
    case VERIFIED = 'VERIFIED';

    public static function labels(): array
    {
        return [
            self::UNVERIFIED->value => 'Sin verificar',
            self::PENDING->value => 'Pendiente',
            self::VERIFIED->value => 'Verificado',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }
}
