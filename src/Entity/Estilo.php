<?php

namespace App\Entity;

use App\Enum\TipoFermentacion;
use App\Repository\EstiloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstiloRepository::class)]
class Estilo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maridaje = null;

    /**
     * @var Collection<int, Bebida>
     */
    #[ORM\OneToMany(targetEntity: Bebida::class, mappedBy: 'estilo')]
    private Collection $bebidas;

    #[ORM\Column(type: 'string', nullable: true, enumType: TipoFermentacion::class)]
    private ?TipoFermentacion $fermentacion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sabor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $aroma = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $carbonatacion = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subestilos')]
    #[ORM\JoinColumn(nullable: true)]
    private ?self $estiloPadre = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'estiloPadre')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $subestilos;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origen = null;


    public function __construct()
    {
        $this->bebidas = new ArrayCollection();
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

    public function setNombre(string $Nombre): static
    {
        $this->nombre = $Nombre;

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

    public function getMaridaje(): ?string
    {
        return $this->maridaje;
    }

    public function setMaridaje(?string $Maridaje): static
    {
        $this->maridaje = $Maridaje;

        return $this;
    }

    /**
     * @return Collection<int, Bebida>
     */
    public function getBebidas(): Collection
    {
        return $this->bebidas;
    }

    public function addBebida(Bebida $bebida): static
    {
        if (!$this->bebidas->contains($bebida)) {
            $this->bebidas->add($bebida);
            $bebida->setEstilo($this);
        }

        return $this;
    }

    public function removeBebida(Bebida $bebida): static
    {
        if ($this->bebidas->removeElement($bebida)) {

            if ($bebida->getEstilo() === $this) {
                $bebida->setEstilo(null);
            }
        }

        return $this;
    }

    public function getFermentacion(): ?TipoFermentacion
    {
        return $this->fermentacion;
    }

    public function setFermentacion(?TipoFermentacion $fermentacion): static
    {
        $this->fermentacion = $fermentacion;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getSabor(): ?string
    {
        return $this->sabor;
    }

    public function setSabor(?string $sabor): static
    {
        $this->sabor = $sabor;

        return $this;
    }

    public function getAroma(): ?string
    {
        return $this->aroma;
    }

    public function setAroma(?string $aroma): static
    {
        $this->aroma = $aroma;

        return $this;
    }

    public function getCarbonatacion(): ?string
    {
        return $this->carbonatacion;
    }

    public function setCarbonatacion(?string $carbonatacion): static
    {
        $this->carbonatacion = $carbonatacion;

        return $this;
    }

    public function getEstiloPadre(): ?self
    {
        return $this->estiloPadre;
    }

    public function setEstiloPadre(?self $estiloPadre): static
    {
        $this->estiloPadre = $estiloPadre;
        return $this;
    }

    public function getSubestilos(): Collection
    {
        return $this->subestilos;
    }

    public function addSubestilo(self $subestilo): static
    {
        if (!$this->subestilos->contains($subestilo)) {
            $this->subestilos->add($subestilo);
            $subestilo->setEstiloPadre($this);
        }
        return $this;
    }

    public function removeSubestilo(self $subestilo): static
    {
        if ($this->subestilos->removeElement($subestilo)) {
            if ($subestilo->getEstiloPadre() === $this) {
                $subestilo->setEstiloPadre(null);
            }
        }
        return $this;
    }

    public function getOrigen(): ?string
    {
        return $this->origen;
    }

    public function setOrigen(?string $origen): static
    {
        $this->origen = $origen;

        return $this;
    }
}
