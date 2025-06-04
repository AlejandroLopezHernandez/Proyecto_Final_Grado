<?php

namespace App\Enum;

enum CategoriaComida: string
{
    case ENTRANTES = 'entrantes';
    case ENSALADAS = 'ensaladas';
    case CARNES = 'carnes';
    case MAR = 'mar';
    case ARROCES_PASTAS = 'arroces_pastas';
    case ENTRE_PANES = 'entre_panes';
    case VEGETARIANO = 'vegetariano';
    case VEGANO = 'vegano';
    case POSTRES = 'postres';
    case OTROS = 'otros';

    // Método para mostrar nombres legibles
    public function nombresCategorias(): string
    {
        return match ($this) {
            self::ENTRANTES => 'Entrantes',
            self::ENSALADAS => 'Ensaladas',
            self::CARNES => 'Carnes',
            self::MAR => 'Pescados y Mariscos',
            self::ARROCES_PASTAS => 'Arroces y Pastas',
            self::ENTRE_PANES => 'Entre Panes',
            self::VEGETARIANO => 'Vegetariano',
            self::VEGANO => 'Vegano',
            self::POSTRES => 'Postres',
            self::OTROS => 'Otros'
        };
    }

    // Método para facilitar la implementación en el CrudController
    public static function eleccionParaCrud(): array
    {
        return array_reduce(
            self::cases(),
            fn(array $choices, self $case) => $choices + [$case->nombresCategorias() => $case],
            []
        );
    }

    // Método para poder hacer elección multiple
    public static function eleccionMultipleParaCrud(): array
    {
        return array_reduce(
            self::cases(),
            fn(array $choices, self $case) => $choices + [$case->nombresCategorias() => $case->value],
            []
        );
    }
}