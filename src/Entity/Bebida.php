<?php

namespace App\Entity;

use App\Enum\FormatoBebida;
use App\Repository\BebidaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MapsId;

#[ORM\Entity(repositoryClass: BebidaRepository::class)]
class Bebida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?float $gradoAlcoholico = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: FormatoBebida::class)]
    private ?FormatoBebida $formato = null;

    #[ORM\Column(nullable: true)]
    private ?float $precio = null;

    #[ORM\Column(nullable: true)]
    private ?int $stock = 0;    // si no se rellena habrá 0 stock

    #[ORM\ManyToOne(inversedBy: 'bebidas')]
    private ?Estilo $estilo = null;

    #[ORM\ManyToOne(inversedBy: 'bebidas')]
    private ?Fabricante $fabricante = null;

    /**
     * @var Collection<int, Proveedor>
     */
    #[ORM\ManyToMany(targetEntity: Proveedor::class, inversedBy: 'bebidas')]
    private Collection $proveedores;

    public function __construct()
    {
        $this->proveedores = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nombre;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $Nombre): static
    {
        $this->nombre = $Nombre;

        return $this;
    }

    public function getGradoAlcoholico(): ?float
    {
        return $this->gradoAlcoholico;
    }

    public function setGradoAlcoholico(?float $GradoAlcoholico): static
    {
        $this->gradoAlcoholico = $GradoAlcoholico;

        return $this;
    }

    /**
     * @return FormatoBebida[]|null
     */
    public function getFormato(): ?FormatoBebida
    {
        return $this->formato;
    }

    public function setFormato(?FormatoBebida $Formato): static
    {
        $this->formato = $Formato;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(?float $Precio): static
    {
        $this->precio = $Precio;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $Stock): static
    {
        $this->stock = $Stock;

        return $this;
    }


    public function getEstilo(): ?Estilo
    {
        return $this->estilo;
    }

    public function setEstilo(?Estilo $Estilo): static
    {
        $this->estilo = $Estilo;

        return $this;
    }

    public function getFabricante(): ?Fabricante
    {
        return $this->fabricante;
    }

    public function setFabricante(?Fabricante $Fabricante): static
    {
        $this->fabricante = $Fabricante;

        return $this;
    }

    /**
     * @return Collection<int, Proveedor>
     */
    public function getProveedores(): Collection
    {
        return $this->proveedores;
    }

    public function addProveedor(Proveedor $proveedor): static
    {
        if (!$this->proveedores->contains($proveedor)) {
            $this->proveedores->add($proveedor);
            $proveedor->addBebida($this); // Para que tambien se registre en el proveedor asociado
        }

        return $this;
    }

    public function removeProveedor(Proveedor $proveedor): static
    {
        if ($this->proveedores->removeElement($proveedor)) {
            $proveedor->removeBebida($this); // Para que tambien se borre en el proveedor asociado
        }
        return $this;
    }
}
