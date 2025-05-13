<?php

namespace App\Enum;

enum VegetarianoVeganoSeleccion: string
{
    case VEGETARIANO = 'vegetariano';
    case VEGANO = 'vegano';
    case REGULAR = 'regular'; // Nueva opción para comidas no vegetarianas/veganas

    // Método para nombres legibles
    public function nombresSeleccion(): string
    {
        return match ($this) {
            self::VEGETARIANO => 'Vegetariano',
            self::VEGANO => 'Vegano',
            self::REGULAR => 'Regular' // O "Tradicional" según prefieras
        };
    }

    // Método para el CRUD
    public static function eleccionParaCrud(): array
    {
        return array_reduce(
            self::cases(),
            fn(array $choices, self $case) => $choices + [$case->nombresSeleccion() => $case],
            []
        );
    }
}