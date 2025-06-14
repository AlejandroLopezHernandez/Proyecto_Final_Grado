<?php

namespace App\Entity;

use App\Repository\MesaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesaRepository::class)]
class Mesa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?int $posicionX = null;

    #[ORM\Column(nullable: true)]
    private ?int $posicionY = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activa = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPosicionX(): ?int
    {
        return $this->posicionX;
    }

    public function setPosicionX(?int $posicionX): static
    {
        $this->posicionX = $posicionX;

        return $this;
    }

    public function getPosicionY(): ?int
    {
        return $this->posicionY;
    }

    public function setPosicionY(?int $posicionY): static
    {
        $this->posicionY = $posicionY;

        return $this;
    }

    public function isActiva(): ?bool
    {
        return $this->activa;
    }

    public function setActiva(?bool $activa): static
    {
        $this->activa = $activa;

        return $this;
    }
}
