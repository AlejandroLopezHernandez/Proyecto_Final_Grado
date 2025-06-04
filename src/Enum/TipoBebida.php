<?php

namespace App\Enum;

enum TipoBebida: string
{
    case REFRESCOS = 'refrescos';
    case CERVEZA = 'cerveza';
    case VINOS = 'vinos';
    case DESTILADOS = 'destilados';
    case LICORES = 'licores';
    case CAFES = 'cafes';
    case SIN_ALCOHOL = 'sin alcohol';

    // Método para mostrar nombres legibles
    public function nombreLegible(): string
    {
        return match ($this) {
            self::REFRESCOS => 'Refrescos',
            self::CERVEZA => 'Cerveza',
            self::VINOS => 'Vinos',
            self::DESTILADOS => 'Destilados',
            self::LICORES => 'Licores',
            self::CAFES => 'Cafés',
            self::SIN_ALCOHOL => 'Sin Alcohol',
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
