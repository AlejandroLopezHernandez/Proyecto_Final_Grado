<?php

namespace App\Enum;

class OpcionesAnadir
{
    public const EXTRA_QUESO = 'Extra queso';
    public const EXTRA_BACON = 'Extra bacon';
    public const EXTRA_HUEVO = 'Extra huevo';
    public const EXTRA_SALSA = 'Extra salsa';
    public const EXTRA_CARNE = 'Extra carne';

    public static function todas(): array
    {
        return [
            'Extra queso' => self::EXTRA_QUESO,
            'Extra bacon' => self::EXTRA_BACON,
            'Extra huevo' => self::EXTRA_HUEVO,
            'Extra salsa' => self::EXTRA_SALSA,
            'Extra carne' => self::EXTRA_CARNE
        ];
    }
}