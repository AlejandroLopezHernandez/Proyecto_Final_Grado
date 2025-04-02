<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Foto = null;

    #[ORM\Column(length: 255)]
    private ?string $Contrasenya = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoto(): ?string
    {
        return $this->Foto;
    }

    public function setFoto(?string $Foto): static
    {
        $this->Foto = $Foto;

        return $this;
    }

    public function getContrasenya(): ?string
    {
        return $this->Contrasenya;
    }

    public function setContrasenya(string $Contrasenya): static
    {
        $this->Contrasenya = $Contrasenya;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
