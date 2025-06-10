<?php

namespace App\Enums;

enum TypeUser: string
{
    case STUDENT = 'student';
    case CLIENT = 'client';
    case ADMIN = 'admin';

    public static function labels(): array
    {
        return [
            self::STUDENT->value => 'Estudiante',
            self::CLIENT->value => 'Cliente',
            self::ADMIN->value => 'Administrador',
        ];
    }
}
