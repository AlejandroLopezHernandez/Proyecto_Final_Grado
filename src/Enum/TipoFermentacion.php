<?php

namespace App\Enum;

enum TipoFermentacion: string
{
    case ALE = 'ale';
    case LAGER = 'lager';
    case LAMBIC = 'lambic';
    case MIXTA = 'mixta';

    // Método para mostrar nombres legibles
    public function nombreLegible(): string
    {
        return match ($this) {
            self::ALE => 'Fermentación Alta (Ale)',
            self::LAGER => 'Fermentación Baja (Lager)',
            self::LAMBIC => 'Fermentación Espontánea (Lambic)',
            self::MIXTA => 'Fermentación Mixta',
        };
    }

    // Método para facilitar la implementación en el CrudController
    public static function eleccionParaCrud(): array
    {
        return array_reduce(
            self::cases(),
            fn(array $choices, self $case) => $choices + [$case->nombreLegible() => $case],
            []
        );
    }

    // Método para poder hacer elección múltiple (devolver solo los valores string)
    public static function eleccionMultipleParaCrud(): array
    {
        return array_reduce(
            self::cases(),
            fn(array $choices, self $case) => $choices + [$case->nombreLegible() => $case->value],
            []
        );
    }
}
