<?php

namespace App\Entity;

use App\Repository\FabricanteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FabricanteRepository::class)]
class Fabricante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    /**
     * @var Collection<int, Bebida>
     */
    #[ORM\OneToMany(targetEntity: Bebida::class, mappedBy: 'Fabricante')]
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

    public function setNombre(?string $Nombre): static
    {
        $this->nombre = $Nombre;

        return $this;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(?string $Pais): static
    {
        $this->pais = $Pais;

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

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $Imagen): static
    {
        $this->imagen = $Imagen;

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
            $bebida->setFabricante($this);
        }

        return $this;
    }

    public function removeBebida(Bebida $bebida): static
    {
        if ($this->bebidas->removeElement($bebida)) {
            // set the owning side to null (unless already changed)
            if ($bebida->getFabricante() === $this) {
                $bebida->setFabricante(null);
            }
        }

        return $this;
    }
}
