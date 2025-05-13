<?php

namespace App\Enum;

enum TipoBebida: string
{
    case Cerveza = 'cerveza';
    case Vinos = 'vinos';
    case Licores = 'licores';
    case NoAlcoholica = 'no alcoholica';
}
