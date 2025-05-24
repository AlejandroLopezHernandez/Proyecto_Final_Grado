<?php

namespace App\Entity;

use App\Enum\EstadoReserva;
use App\Repository\ReservaRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservaRepository::class)]
class Reserva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombreCliente = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailCliente = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telefonoCliente = null;

    #[ORM\Column(nullable: true)]
    private ?int $numeroComensales = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $fechaHoraReserva = null;

    #[ORM\Column(nullable: true)]
    private ?int $numeroMesa = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infoAdicional = null;

    #[ORM\Column(nullable: true, enumType: EstadoReserva::class)]
    private ?EstadoReserva $estado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreCliente(): ?string
    {
        return $this->nombreCliente;
    }

    public function setNombreCliente(?string $nombreCliente): static
    {
        $this->nombreCliente = $nombreCliente;

        return $this;
    }

    public function getEmailCliente(): ?string
    {
        return $this->emailCliente;
    }

    public function setEmailCliente(?string $emailCliente): static
    {
        $this->emailCliente = $emailCliente;

        return $this;
    }

    public function getTelefonoCliente(): ?string
    {
        return $this->telefonoCliente;
    }

    public function setTelefonoCliente(?string $telefonoCliente): static
    {
        $this->telefonoCliente = $telefonoCliente;

        return $this;
    }

    public function getNumeroComensales(): ?int
    {
        return $this->numeroComensales;
    }

    public function setNumeroComensales(?int $numeroComensales): static
    {
        $this->numeroComensales = $numeroComensales;

        return $this;
    }

    public function getFechaHoraReserva(): ?DateTimeInterface
    {
        return $this->fechaHoraReserva;
    }

    public function setFechaHoraReserva(?DateTimeInterface $fechaHoraReserva): static
    {
        $this->fechaHoraReserva = $fechaHoraReserva;

        return $this;
    }

    public function getNumeroMesa(): ?int
    {
        return $this->numeroMesa;
    }

    public function setNumeroMesa(?int $numeroMesa): static
    {
        $this->numeroMesa = $numeroMesa;

        return $this;
    }

    public function getInfoAdicional(): ?string
    {
        return $this->infoAdicional;
    }

    public function setInfoAdicional(?string $infoAdicional): static
    {
        $this->infoAdicional = $infoAdicional;

        return $this;
    }

    public function getEstado(): ?EstadoReserva
    {
        return $this->estado;
    }

    public function setEstado(?EstadoReserva $estado): static
    {
        $this->estado = $estado;

        return $this;
    }
}
