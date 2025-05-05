<?php

namespace App\Enum;

enum FormatoBebida: string
{
    case Pinta = 'pinta';
    case MediaPinta = 'media pinta';
    case Canya = 'caña';
    case Copa = 'copa';
    case CafeLeche = 'café con leche';
    case CafeExpresso = 'café expresso';
}
