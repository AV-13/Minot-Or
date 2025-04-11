<?php

namespace App\Entity;

use App\Repository\EntrepotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepotRepository::class)]
class Entrepot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseEntrepot = null;

    #[ORM\Column]
    private ?int $capaciteStockage = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, mappedBy: 'Entrepot')]
    private Collection $produits;

    /**
     * @var Collection<int, Camion>
     */
    #[ORM\OneToMany(targetEntity: Camion::class, mappedBy: 'Entrepot')]
    private Collection $Camions;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->Camions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresseEntrepot(): ?string
    {
        return $this->adresseEntrepot;
    }

    public function setAdresseEntrepot(string $adresseEntrepot): static
    {
        $this->adresseEntrepot = $adresseEntrepot;

        return $this;
    }

    public function getCapaciteStockage(): ?int
    {
        return $this->capaciteStockage;
    }

    public function setCapaciteStockage(int $capaciteStockage): static
    {
        $this->capaciteStockage = $capaciteStockage;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addEntrepot($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeEntrepot($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Camion>
     */
    public function getCamions(): Collection
    {
        return $this->Camions;
    }

    public function addCamion(Camion $camion): static
    {
        if (!$this->Camions->contains($camion)) {
            $this->Camions->add($camion);
            $camion->setEntrepot($this);
        }

        return $this;
    }

    public function removeCamion(Camion $camion): static
    {
        if ($this->Camions->removeElement($camion)) {
            // set the owning side to null (unless already changed)
            if ($camion->getEntrepot() === $this) {
                $camion->setEntrepot(null);
            }
        }

        return $this;
    }
}
