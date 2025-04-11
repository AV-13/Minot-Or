<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomFournisseur = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseFournisseur = null;

    /**
     * @var Collection<int, Avoir>
     */
    #[ORM\ManyToMany(targetEntity: Avoir::class, mappedBy: 'Fournisseur')]
    private Collection $ProduitsFournisseur;

    public function __construct()
    {
        $this->ProduitsFournisseur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFournisseur(): ?string
    {
        return $this->nomFournisseur;
    }

    public function setNomFournisseur(string $nomFournisseur): static
    {
        $this->nomFournisseur = $nomFournisseur;

        return $this;
    }

    public function getAdresseFournisseur(): ?string
    {
        return $this->adresseFournisseur;
    }

    public function setAdresseFournisseur(string $adresseFournisseur): static
    {
        $this->adresseFournisseur = $adresseFournisseur;

        return $this;
    }

    /**
     * @return Collection<int, Avoir>
     */
    public function getProduitsFournisseur(): Collection
    {
        return $this->ProduitsFournisseur;
    }

    public function addProduitsFournisseur(Avoir $produitsFournisseur): static
    {
        if (!$this->ProduitsFournisseur->contains($produitsFournisseur)) {
            $this->ProduitsFournisseur->add($produitsFournisseur);
            $produitsFournisseur->addFournisseur($this);
        }

        return $this;
    }

    public function removeProduitsFournisseur(Avoir $produitsFournisseur): static
    {
        if ($this->ProduitsFournisseur->removeElement($produitsFournisseur)) {
            $produitsFournisseur->removeFournisseur($this);
        }

        return $this;
    }
}
