<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomProduit = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column]
    private ?float $prixHT = null;

    #[ORM\Column]
    private ?float $prixTTC = null;

    #[ORM\Column]
    private ?float $poidsUnitaire = null;

    #[ORM\Column]
    private ?int $quantiteArticleEntreproser = null;

    /**
     * @var Collection<int, Entrepot>
     */
    #[ORM\ManyToMany(targetEntity: Entrepot::class, inversedBy: 'produits')]
    private Collection $Entrepot;

    /**
     * @var Collection<int, Avoir>
     */
    #[ORM\ManyToMany(targetEntity: Avoir::class, mappedBy: 'Produit')]
    private Collection $FournisseursProduit;

    public function __construct()
    {
        $this->Entrepot = new ArrayCollection();
        $this->FournisseursProduit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixHT(): ?float
    {
        return $this->prixHT;
    }

    public function setPrixHT(float $prixHT): static
    {
        $this->prixHT = $prixHT;

        return $this;
    }

    public function getPrixTTC(): ?float
    {
        return $this->prixTTC;
    }

    public function setPrixTTC(float $prixTTC): static
    {
        $this->prixTTC = $prixTTC;

        return $this;
    }

    public function getPoidsUnitaire(): ?float
    {
        return $this->poidsUnitaire;
    }

    public function setPoidsUnitaire(float $poidsUnitaire): static
    {
        $this->poidsUnitaire = $poidsUnitaire;

        return $this;
    }

    public function getQuantiteArticleEntreproser(): ?int
    {
        return $this->quantiteArticleEntreproser;
    }

    public function setQuantiteArticleEntreproser(int $quantiteArticleEntreproser): static
    {
        $this->quantiteArticleEntreproser = $quantiteArticleEntreproser;

        return $this;
    }

    /**
     * @return Collection<int, Entrepot>
     */
    public function getEntrepot(): Collection
    {
        return $this->Entrepot;
    }

    public function addEntrepot(Entrepot $entrepot): static
    {
        if (!$this->Entrepot->contains($entrepot)) {
            $this->Entrepot->add($entrepot);
        }

        return $this;
    }

    public function removeEntrepot(Entrepot $entrepot): static
    {
        $this->Entrepot->removeElement($entrepot);

        return $this;
    }

    /**
     * @return Collection<int, Avoir>
     */
    public function getFournisseursProduit(): Collection
    {
        return $this->FournisseursProduit;
    }

    public function addFournisseursProduit(Avoir $fournisseursProduit): static
    {
        if (!$this->FournisseursProduit->contains($fournisseursProduit)) {
            $this->FournisseursProduit->add($fournisseursProduit);
            $fournisseursProduit->addProduit($this);
        }

        return $this;
    }

    public function removeFournisseursProduit(Avoir $fournisseursProduit): static
    {
        if ($this->FournisseursProduit->removeElement($fournisseursProduit)) {
            $fournisseursProduit->removeProduit($this);
        }

        return $this;
    }
}
