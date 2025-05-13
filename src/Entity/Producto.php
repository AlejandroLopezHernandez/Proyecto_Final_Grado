<?php

namespace App\Entity;

use App\Enum\CategoriaProducto;
use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?float $coste = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $medida = null;

    #[ORM\Column(nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: CategoriaProducto::class)]
    private ?CategoriaProducto $categoria = null;

    #[ORM\ManyToMany(targetEntity: Proveedor::class, inversedBy: 'productos')]
    private Collection $proveedores;

    #[ORM\ManyToMany(targetEntity: Comida::class, inversedBy: 'productos')]
    private Collection $comidas;

    public function __construct()
    {        
        $this->proveedores = new ArrayCollection();
        $this->comidas = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nombre ?? '';
    }

    
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

    public function getCoste(): ?float
    {
        return $this->coste;
    }

    public function setCoste(?float $coste): static
    {
        $this->coste = $coste;
        return $this;
    }

    public function getMedida(): ?string
    {
        return $this->medida;
    }

    public function setMedida(?string $medida): static
    {
        $this->medida = $medida;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getCategoria(): ?CategoriaProducto
    {
        return $this->categoria;
    }

    public function setCategoria(?CategoriaProducto $categoria): static
    {
        $this->categoria = $categoria;
        return $this;
    }

    // Relación con Proveedores
    public function getProveedores(): Collection
    {
        return $this->proveedores;
    }

    public function addProveedor(Proveedor $proveedor): static
    {
        if (!$this->proveedores->contains($proveedor)) {
            $this->proveedores->add($proveedor);
        }
        return $this;
    }

    public function removeProveedor(Proveedor $proveedor): static
    {
        $this->proveedores->removeElement($proveedor);
        return $this;
    }

    // Relación con Comidas (bidireccional)
    public function getComidas(): Collection
    {
        return $this->comidas;
    }

    public function addComida(Comida $comida): static
    {
        if (!$this->comidas->contains($comida)) {
            $this->comidas->add($comida);
            $comida->addProducto($this);
        }
        return $this;
    }

    public function removeComida(Comida $comida): static
    {
        if ($this->comidas->removeElement($comida)) {
            $comida->removeProducto($this);
        }
        return $this;
    }
}