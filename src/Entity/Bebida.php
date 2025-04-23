<?php

namespace App\Entity;

use App\Enum\FormatoBebida;
use App\Repository\BebidaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BebidaRepository::class)]
class Bebida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nombre = null;

    #[ORM\Column(nullable: true)]
    private ?float $GradoAlcoholico = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true, enumType: FormatoBebida::class)]
    private ?array $Formato = null;

    #[ORM\Column(nullable: true)]
    private ?float $Precio = null;

    #[ORM\Column(nullable: true)]
    private ?int $Stock = null;

    #[ORM\OneToOne(inversedBy: 'Bebida')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Producto $producto = null;

    #[ORM\ManyToOne(inversedBy: 'bebidas')]
    private ?Estilo $Estilo = null;

    #[ORM\ManyToOne(inversedBy: 'bebidas')]
    private ?Fabricante $Fabricante = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->Nombre;
    }

    public function setNombre(?string $Nombre): static
    {
        $this->Nombre = $Nombre;

        return $this;
    }

    public function getGradoAlcoholico(): ?float
    {
        return $this->GradoAlcoholico;
    }

    public function setGradoAlcoholico(?float $GradoAlcoholico): static
    {
        $this->GradoAlcoholico = $GradoAlcoholico;

        return $this;
    }

    /**
     * @return FormatoBebida[]|null
     */
    public function getFormato(): ?array
    {
        return $this->Formato;
    }

    public function setFormato(?array $Formato): static
    {
        $this->Formato = $Formato;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->Precio;
    }

    public function setPrecio(?float $Precio): static
    {
        $this->Precio = $Precio;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->Stock;
    }

    public function setStock(?int $Stock): static
    {
        $this->Stock = $Stock;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }

    public function getEstilo(): ?Estilo
    {
        return $this->Estilo;
    }

    public function setEstilo(?Estilo $Estilo): static
    {
        $this->Estilo = $Estilo;

        return $this;
    }

    public function getFabricante(): ?Fabricante
    {
        return $this->Fabricante;
    }

    public function setFabricante(?Fabricante $Fabricante): static
    {
        $this->Fabricante = $Fabricante;

        return $this;
    }
}
