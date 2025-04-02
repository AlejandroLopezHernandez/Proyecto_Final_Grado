<?php

namespace App\Entity;

use App\Repository\ComandaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComandaRepository::class)]
class Comanda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Contenido = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenido(): ?string
    {
        return $this->Contenido;
    }

    public function setContenido(?string $Contenido): static
    {
        $this->Contenido = $Contenido;

        return $this;
    }
}
