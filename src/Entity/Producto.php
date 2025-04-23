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
    private ?string $Nombre = null;

    #[ORM\Column(nullable: true)]
    private ?float $Coste = null;

    #[ORM\Column(nullable: true)]
    private ?int $Stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Descripcion = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true, enumType: CategoriaProducto::class)]
    private ?array $Categoria = null;

    /**
     * @var Collection<int, Comida>
     */
    #[ORM\ManyToMany(targetEntity: Comida::class, mappedBy: 'Producto')]
    private Collection $comidas;

    /**
     * @var Collection<int, Proveedor>
     */
    #[ORM\ManyToMany(targetEntity: Proveedor::class, inversedBy: 'productos')]
    private Collection $Proveedor;

    #[ORM\OneToOne(mappedBy: 'producto', targetEntity: Bebida::class, cascade: ['persist', 'remove'])]
    private ?Bebida $Bebida = null;

    public function __construct()
    {
        $this->comidas = new ArrayCollection();
        $this->Proveedor = new ArrayCollection();
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

    public function getCoste(): ?float
    {
        return $this->Coste;
    }

    public function setCoste(?float $Coste): static
    {
        $this->Coste = $Coste;

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

    public function getDescripcion(): ?string
    {
        return $this->Descripcion;
    }

    public function setDescripcion(?string $Descripcion): static
    {
        $this->Descripcion = $Descripcion;

        return $this;
    }

    /**
     * @return CategoriaProducto[]|null
     */
    public function getCategoria(): ?array
    {
        return $this->Categoria;
    }

    public function setCategoria(?array $Categoria): static
    {
        $this->Categoria = $Categoria;

        return $this;
    }

    /**
     * @return Collection<int, Comida>
     */
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

    /**
     * @return Collection<int, Proveedor>
     */
    public function getProveedor(): Collection
    {
        return $this->Proveedor;
    }

    public function addProveedor(Proveedor $proveedor): static
    {
        if (!$this->Proveedor->contains($proveedor)) {
            $this->Proveedor->add($proveedor);
        }

        return $this;
    }

    public function removeProveedor(Proveedor $proveedor): static
    {
        $this->Proveedor->removeElement($proveedor);

        return $this;
    }

    public function getBebida(): ?Bebida
    {
        return $this->Bebida;
    }

    public function setBebida(?Bebida $bebida): self
    {
        $this->Bebida = $bebida;
        if ($bebida !== null && $bebida->getProducto() !== $this) {
            $bebida->setProducto($this);
        }
        return $this;
    }
}
