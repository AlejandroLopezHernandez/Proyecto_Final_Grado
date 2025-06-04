<?php

namespace App\Enum;

class PuntoCoccion
{
    public const POCO_HECHO = 'Poco hecho';
    public const AL_PUNTO = 'Al punto';
    public const BIEN_HECHO = 'Bien hecho';

    public static function todas(): array
    {
        return [
            'Poco hecho' => self::POCO_HECHO,
            'Al punto' => self::AL_PUNTO,
            'Bien hecho' => self::BIEN_HECHO
        ];
    }
}