<?php

namespace App\Enums;

enum CategoryProduct: string
{
    case KNIVES = 'knives';
    case BOARDS = 'boards';
    case UNIFORMS = 'uniforms';
    case OTHERS = 'others';

    public static function labels(): array
    {
        return [
            self::KNIVES->value => 'Cuchillos',
            self::BOARDS->value => 'Tablas',
            self::UNIFORMS->value => 'Uniformes',
            self::OTHERS->value => 'Otros',
        ];
    }
}
