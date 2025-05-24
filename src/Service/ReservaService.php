<?php

namespace App\Service;

use App\Entity\Reserva;
use App\Enum\EstadoReserva;
use Doctrine\ORM\EntityManagerInterface;

class ReservaService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function crearReserva(Reserva $reserva): bool
    {
        // Simular validación con "Google Reservas"
        $exito = $this->simularAPIGoogleReservas($reserva);

        if ($exito) {
            $reserva->setEstado(EstadoReserva::Confirmado);
            $this->entityManager->persist($reserva);
            $this->entityManager->flush();
            return true;
        }

        return false;
    }

    private function simularAPIGoogleReservas(Reserva $reserva): bool
    {
        // Simular que estamos contactando con Google Reservas
        $fechaHoraReserva = $reserva->getFechaHoraReserva();

        if (!$fechaHoraReserva) {
            return false;
        }

        $hora = (int) $fechaHoraReserva->format('H');

        // El restaurante está abierto de 12:00 a 23:00
        if ($hora < 12 || $hora >= 23) {
            return false;
        }
        // Verificar que la reserva no sea en el pasado
        $ahora = new \DateTime();
        if ($fechaHoraReserva < $ahora) {
            return false;
        }

        // Comprobar disponibilidad simulada
        $reservasExistentes = $this->entityManager->getRepository(Reserva::class)
            ->findBy([
                'fechaHoraReserva' => $fechaHoraReserva,
                'estado' => EstadoReserva::Confirmado
            ]);

        // Simulamos que tenemos 10 mesas disponibles
        if (count($reservasExistentes) >= 10) {
            return false;
        }

        // Asignar mesa automáticamente
        $mesasDisponibles = range(1, 10);
        foreach ($reservasExistentes as $reservaExistente) {
            $numeroMesa = $reservaExistente->getNumeroMesa();
            $clave = array_search($numeroMesa, $mesasDisponibles);
            if ($clave !== false) {
                unset($mesasDisponibles[$clave]);
            }
        }

        if (empty($mesasDisponibles)) {
            return false;
        }

        $reserva->setNumeroMesa(reset($mesasDisponibles));

        return true;
    }

    public function cancelarReserva(Reserva $reserva): bool
    {
        $reserva->setEstado(EstadoReserva::Cancelado);
        $this->entityManager->flush();
        return true;
    }
}
