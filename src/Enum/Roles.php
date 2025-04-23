<?php

namespace App\Enum;

enum Roles: string
{
    case Trabajador = 'trabajador';
    case Admin = 'admin';
    case Manager = 'manager';
}
