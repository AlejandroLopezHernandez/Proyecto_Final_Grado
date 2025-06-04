<?php

namespace App\Enum;

class OpcionesQuitar
{
    public const SIN_CEBOLLA = 'Sin cebolla';
    public const SIN_TOMATE = 'Sin tomate';
    public const SIN_QUESO = 'Sin queso';
    public const SIN_SALSA = 'Sin salsa';
    public const SIN_HUEVO = 'Sin huevo';
    public const SIN_BACON = 'Sin bacon';
    public const SIN_CARNE = 'Sin carne';

    public static function todas(): array
{
    return [
        'Sin cebolla' => self::SIN_CEBOLLA,
        'Sin tomate' => self::SIN_TOMATE,
        'Sin queso' => self::SIN_QUESO,
        'Sin salsa' => self::SIN_SALSA,
        'Sin huevo' => self::SIN_HUEVO,
        'Sin bacon' => self::SIN_BACON,
        'Sin carne' => self::SIN_CARNE
    ];
}

}
