<?php

namespace App\Entity;

use App\Repository\AvoirRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvoirRepository::class)]
class Avoir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'FournisseursProduit')]
    private Collection $Produit;

    /**
     * @var Collection<int, Fournisseur>
     */
    #[ORM\ManyToMany(targetEntity: Fournisseur::class, inversedBy: 'ProduitsFournisseur')]
    private Collection $Fournisseur;

    public function __construct()
    {
        $this->Produit = new ArrayCollection();
        $this->Fournisseur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->Produit;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->Produit->contains($produit)) {
            $this->Produit->add($produit);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        $this->Produit->removeElement($produit);

        return $this;
    }

    /**
     * @return Collection<int, Fournisseur>
     */
    public function getFournisseur(): Collection
    {
        return $this->Fournisseur;
    }

    public function addFournisseur(Fournisseur $fournisseur): static
    {
        if (!$this->Fournisseur->contains($fournisseur)) {
            $this->Fournisseur->add($fournisseur);
        }

        return $this;
    }

    public function removeFournisseur(Fournisseur $fournisseur): static
    {
        $this->Fournisseur->removeElement($fournisseur);

        return $this;
    }
}
