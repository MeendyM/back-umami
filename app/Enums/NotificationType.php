<?php

namespace App\Enums;

enum NotificationType: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
    case ADMIN = 'admin';

    public static function labels(): array
    {
        return [
            self::SUCCESS->value => 'Éxito',
            self::ERROR->value => 'Error',
            self::WARNING->value => 'Advertencia',
            self::INFO->value => 'Información',
            self::ADMIN->value => 'Administrador',
        ];
    }
}
