<?php

namespace App\Entity;

use App\Enum\OpcionesQuitar;
use App\Enum\OpcionesAnadir;
use App\Enum\PuntoCoccion;
use App\Dto\OpcionesComida;
use App\Enum\CategoriaComida;
use App\Enum\VegetarianoVeganoSeleccion;
use App\Repository\ComidaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComidaRepository::class)]
class Comida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?float $precio = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $receta = null;

    #[ORM\Column(type: Types::JSON)]
    private array $categoria = [];

    #[ORM\Column(nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: VegetarianoVeganoSeleccion::class)]
    private ?VegetarianoVeganoSeleccion $dieta = null;

    #[ORM\ManyToMany(targetEntity: Producto::class, mappedBy: 'comidas')]
    private Collection $productos;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $opciones = [];

    public function __construct()
    {
        $this->productos = new ArrayCollection();
        $this->opciones = array_merge(
            OpcionesQuitar::todas(),// funciones de cada enum
            OpcionesAnadir::todas(),
            PuntoCoccion::todas()
        );
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

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(?float $precio): static
    {
        $this->precio = $precio;
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

    public function getReceta(): ?string
    {
        return $this->receta;
    }

    public function setReceta(?string $receta): static
    {
        $this->receta = $receta;
        return $this;
    }

    public function getCategoria(): array
    {
        return $this->categoria;
    }

    public function setCategoria(array $categorias): self
    {
        $this->categoria = $categorias;
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

    public function getDieta(): ?VegetarianoVeganoSeleccion
    {
        return $this->dieta;
    }

    public function setDieta(?VegetarianoVeganoSeleccion $dieta): static
    {
        $this->dieta = $dieta;
        return $this;
    }

    // Relación con Producto (bidireccional)
    public function getProductos(): Collection
    {
        return $this->productos;
    }

    public function addProducto(Producto $producto): static
    {
        if (!$this->productos->contains($producto)) {
            $this->productos->add($producto);
            $producto->addComida($this);
        }
        return $this;
    }

    public function removeProducto(Producto $producto): static
    {
        if ($this->productos->removeElement($producto)) {
            $producto->removeComida($this);
        }
        return $this;
    }

    public function getOpciones(): ?array
    {
        return $this->opciones ?? [];
    }

    public function setOpciones(?array $opciones): self
    {
        $this->opciones = $opciones;
        return $this;
    }
}
