<?php

namespace App\Entity;

use App\Enum\TipoBebida;
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

    #[ORM\Column(type: 'string', nullable: true, enumType: TipoBebida::class)]
    private ?TipoBebida $tipoBebida = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maridaje = null;

    /**
     * @var Collection<int, Bebida>
     */
    #[ORM\OneToMany(targetEntity: Bebida::class, mappedBy: 'Estilo')]
    private Collection $bebidas;

    public function __construct()
    {
        $this->bebidas = new ArrayCollection();
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

    public function getTipoBebida(): ?TipoBebida
    {
        return $this->tipoBebida;
    }

    public function setTipoBebida(?TipoBebida $TipoBebida): static
    {
        $this->tipoBebida = $TipoBebida;

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
            // set the owning side to null (unless already changed)
            if ($bebida->getEstilo() === $this) {
                $bebida->setEstilo(null);
            }
        }

        return $this;
    }
}
