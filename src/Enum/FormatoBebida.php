<?php

namespace App\Enum;

enum FormatoBebida: string
{
    // Cervezas
    case MEDIA_PINTA = 'media_pinta';  // 25cl
    case PINTA = 'pinta';              // 50cl
    case TERCIO = 'tercio';            // 33cl
    case BOTELLA_500 = 'botella_500'; // 50cl
    case LATA = 'lata';                // 44cl
    case BOTELLA_750= 'botella_750'; // 75cl 

        // Vinos
    case COPA_VINO = 'copa_vino';      // 15cl
    case BOTELLA_VINO = 'botella_vino'; // 75cl

        // Refrescos
    case REFRESCO_20CL = 'refresco_20cl';
    case REFRESCO_23_7CL = 'refresco_23.7cl';
    case REFRESCO_30CL = 'refresco_30cl';

        // Alcohol (copas/chupitos)
    case COPA_ALCOHOL = 'copa_alcohol'; // ron, ginebra, ...
    case CHUPITO = 'chupito';          // 5cl

        // Cafés
    case TAZA_CAFE = 'taza_cafe';
    case VASO_CAFE = 'vaso_cafe';

    // Método para mostrar nombres legibles
    public function nombresFormatos(): string
    {
        return match ($this) {
            self::MEDIA_PINTA => 'Media Pinta (25cl)',
            self::PINTA => 'Pinta (50cl)',
            self::TERCIO => 'Tercio (33cl)',
            self::BOTELLA_500 => 'Botella Cerveza (50cl)',
            self::LATA => 'Lata (44cl)',
            self::BOTELLA_750 => 'Botella 75cl (Cerveza)',
            self::COPA_VINO => 'Copa Vino (15cl)',
            self::BOTELLA_VINO => 'Botella Vino (75cl)',
            self::REFRESCO_20CL => 'Refresco 20cl',
            self::REFRESCO_23_7CL => 'Refresco 23.7cl',
            self::REFRESCO_30CL => 'Refresco 30cl',
            self::COPA_ALCOHOL => 'Copa Alcohol',
            self::CHUPITO => 'Chupito (5cl)',
            self::TAZA_CAFE => 'Taza Café',
            self::VASO_CAFE => 'Vaso Café',
        };
    }
    // Metodo para facilitar la implementacion del Enum en el crudController de Bebida
    public static function eleccionParaCrud(): array
    {
        return array_reduce(
            self::cases(),
            fn(array $choices, self $case) => $choices + [$case->nombresFormatos() => $case],
            []
        );
    }
}
