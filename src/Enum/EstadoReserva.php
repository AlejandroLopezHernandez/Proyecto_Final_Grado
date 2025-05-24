<?php

namespace App\Enum;

enum EstadoReserva: string
{
    case Pendiente = 'pendiente';
    case Confirmado = 'confirmado';
    case Cancelado = 'cancelado';
    case Completado = 'completado';
    public function getLabel(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Confirmado => 'Confirmada',
            self::Cancelado => 'Cancelada',
            self::Completado => 'Completada',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::Pendiente => 'bg-warning',
            self::Confirmado => 'bg-success',
            self::Cancelado => 'bg-danger',
            self::Completado => 'bg-info',
        };
    }
}
