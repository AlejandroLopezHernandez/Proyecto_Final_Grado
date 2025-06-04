<?php

namespace App\Enum;

enum CategoriaProducto: string
{
    // Categorías principales
    case COMIDA = 'comida';
    case OTROS = 'otros';

    // Método para mostrar nombres legibles
    public function nombresCategorias(): string
    {
        return match ($this) {
            self::COMIDA => 'Comida',
            self::OTROS => 'Otros productos',
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
}