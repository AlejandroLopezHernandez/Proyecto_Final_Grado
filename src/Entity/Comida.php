<?php

namespace App\Entity;

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
    private ?string $Nombre = null;

    #[ORM\Column(nullable: true)]
    private ?float $Precio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Ingredientes = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: CategoriaComida::class)]
    private ?CategoriaComida $Categoria = null;

    #[ORM\Column(nullable: true)]
    private ?int $Stock = null;

    // /**
    //  * @var Collection<int, Producto>
    //  */
    // #[ORM\ManyToMany(targetEntity: Producto::class, inversedBy: 'comidas')]
    // private Collection $Producto;

    #[ORM\Column(type: 'string', nullable: true, enumType: VegetarianoVeganoSeleccion::class)]
    private ?VegetarianoVeganoSeleccion $Dieta = null;
    //private ?array $Dieta = null;

    /**
     * @var Collection<int, ProductoComida>
     */
    #[ORM\OneToMany(targetEntity: ProductoComida::class, mappedBy: 'Comida', cascade: ['persist'])]
    private Collection $productoComidas;

    public function __construct()
    {
        //$this->Producto = new ArrayCollection();
        $this->productoComidas = new ArrayCollection();
    }

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

    public function getPrecio(): ?float
    {
        return $this->Precio;
    }

    public function setPrecio(?float $Precio): static
    {
        $this->Precio = $Precio;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->Descripcion;
    }

    public function setDescripcion(?string $Descripcion): static
    {
        $this->Descripcion = $Descripcion;

        return $this;
    }

    public function getIngredientes(): ?string
    {
        return $this->Ingredientes;
    }

    public function setIngredientes(?string $Ingredientes): static
    {
        $this->Ingredientes = $Ingredientes;

        return $this;
    }

    public function getCategoria(): ?CategoriaComida
    {
        return $this->Categoria;
    }

    public function setCategoria(?CategoriaComida $Categoria): static
    {
        $this->Categoria = $Categoria;

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

    /**
     * @return Collection<int, Producto>
     */
    // public function getProducto(): Collection
    // {
    //     return $this->Producto;
    // }

    // public function addProducto(Producto $producto): static
    // {
    //     if (!$this->Producto->contains($producto)) {
    //         $this->Producto->add($producto);
    //     }

    //     return $this;
    // }

    // public function removeProducto(Producto $producto): static
    // {
    //     $this->Producto->removeElement($producto);

    //     return $this;
    // }

    /**
     * @return VegetarianoVeganoSeleccion[]|null
     */
    public function getDieta(): ?VegetarianoVeganoSeleccion
    {
        return $this->Dieta;
    }

    public function setDieta(VegetarianoVeganoSeleccion $Dieta): static
    {
        $this->Dieta = $Dieta;

        return $this;
    }

    /**
     * @return Collection<int, ProductoComida>
     */
    public function getProductoComidas(): Collection
    {
        return $this->productoComidas;
    }

    public function addProductoComida(ProductoComida $productoComida): static
    {
        if (!$this->productoComidas->contains($productoComida)) {
            $this->productoComidas->add($productoComida);
            $productoComida->setComida($this);
        }

        return $this;
    }

    public function removeProductoComida(ProductoComida $productoComida): static
    {
        if ($this->productoComidas->removeElement($productoComida)) {
            // set the owning side to null (unless already changed)
            if ($productoComida->getComida() === $this) {
                $productoComida->setComida(null);
            }
        }
        return $this;
    }
}
