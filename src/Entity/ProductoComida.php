<?php

namespace App\Entity;

use App\Repository\ProductoComidaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoComidaRepository::class)]
class ProductoComida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productoComidas')]
    private ?Comida $Comida = null;

    #[ORM\ManyToOne(inversedBy: 'productoComidas')]
    private ?Producto $Producto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComida(): ?Comida
    {
        return $this->Comida;
    }

    public function setComida(?Comida $Comida): static
    {
        $this->Comida = $Comida;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->Producto;
    }

    public function setProducto(?Producto $Producto): static
    {
        $this->Producto = $Producto;

        return $this;
    }
    public function __tostring(): string
    {
        return $this->getProducto()->getNombre();
    }
}
