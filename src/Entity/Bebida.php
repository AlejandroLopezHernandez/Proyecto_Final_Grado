<?php

namespace App\Entity;

use App\Enum\FormatoBebida;
use App\Enum\TipoBebida;
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

    #[ORM\Column(type: 'string', nullable: true, enumType: TipoBebida::class)]
    private ?TipoBebida $tipoBebida = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: FormatoBebida::class)]
    private ?FormatoBebida $formato = null;

    #[ORM\Column(nullable: true)]
    private ?float $coste = null;

    #[ORM\Column(nullable: true)]
    private ?float $pvp = null;

    #[ORM\Column(nullable: true)]
    private ?int $stock = 0;    // si no se rellena habrá 0 stock

    #[ORM\ManyToOne(targetEntity:Estilo::class,inversedBy: 'bebidas')]
    private ?Estilo $estilo = null;

    #[ORM\ManyToOne(inversedBy: 'bebidas')]
    private ?Fabricante $fabricante = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    /**
     * @var Collection<int, Proveedor>
     */
    #[ORM\ManyToMany(targetEntity: Proveedor::class, inversedBy: 'bebidas')]
    private Collection $proveedores;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lupulos = null;

    public function __construct()
    {
        $this->proveedores = new ArrayCollection();
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
     * @return TipoBebida[]|null
     */
    public function getTipoBebida(): ?TipoBebida
    {
        return $this->tipoBebida;
    }

    public function setTipoBebida(?TipoBebida $TipoBebida): static
    {
        $this->tipoBebida = $TipoBebida;

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

    public function getCoste(): ?float
    {
        return $this->coste;
    }

    public function setCoste(?float $Coste): static
    {
        $this->coste = $Coste;

        return $this;
    }

    public function getPvp(): ?float
    {
        return $this->pvp;
    }

    public function setPvp(?float $Pvp): static
    {
        $this->pvp = $Pvp;

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


    /**
     * Mismo tipo de persistencia bidireccional que en Proveedor(sin array)
     */
    public function getFabricante(): ?Fabricante
    {
        return $this->fabricante;
    }
    
    public function setFabricante(?Fabricante $fabricante): static
    {
        $this->fabricante = $fabricante;
    
    
        if ($fabricante !== null && !$fabricante->getBebidas()->contains($this)) {
            $fabricante->addBebida($this);
        }
    
        return $this;
    }
    
    public function removeFabricante(): static
    {
        if ($this->fabricante !== null) {
            $fabricante = $this->fabricante;
            $this->fabricante = null;
    
            if ($fabricante->getBebidas()->contains($this)) {
                $fabricante->removeBebida($this);
            }
        }
    
        return $this;
    }


     /**
     * Mismo tipo de persistencia bidireccional que en Fabricante
     */
    public function getEstilo(): ?Estilo
    {
        return $this->estilo;
    }
    
    public function setEstilo(?Estilo $estilo): static
    {
        $this->estilo = $estilo;
    
        
        if ($estilo !== null && !$estilo->getBebidas()->contains($this)) {
            $estilo->addBebida($this);
        }
    
        return $this;
    }
    
    public function removeEstilo(): static
    {
        if ($this->estilo !== null) {
            $estilo = $this->estilo;
            $this->estilo = null;
    
            if ($estilo->getBebidas()->contains($this)) {
                $estilo->removeBebida($this);
            }
        }
    
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $Descripcion): static
    {
        $this->descripcion = $Descripcion;

        return $this;
    }

    public function getLupulos(): ?string
    {
        return $this->lupulos;
    }

    public function setLupulos(?string $lupulos): static
    {
        $this->lupulos = $lupulos;

        return $this;
    }
    
}
