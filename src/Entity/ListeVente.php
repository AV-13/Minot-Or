<?php

namespace App\Entity;

use App\Repository\ListeVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListeVenteRepository::class)]
class ListeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $prixProduits = null;

    #[ORM\Column]
    private ?int $reductionGlobale = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEmission = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\OneToOne(mappedBy: 'ListeVente', cascade: ['persist', 'remove'])]
    private ?Facture $Facture = null;

    /**
     * @var Collection<int, Contenir>
     */
    #[ORM\OneToMany(targetEntity: Contenir::class, mappedBy: 'ListeVente', orphanRemoval: true)]
    private Collection $ContenirProduits;

    public function __construct()
    {
        $this->ContenirProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixProduits(): ?float
    {
        return $this->prixProduits;
    }

    public function setPrixProduits(float $prixProduits): static
    {
        $this->prixProduits = $prixProduits;

        return $this;
    }

    public function getReductionGlobale(): ?int
    {
        return $this->reductionGlobale;
    }

    public function setReductionGlobale(int $reductionGlobale): static
    {
        $this->reductionGlobale = $reductionGlobale;

        return $this;
    }

    public function getDateEmission(): ?\DateTimeInterface
    {
        return $this->dateEmission;
    }

    public function setDateEmission(\DateTimeInterface $dateEmission): static
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(?\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->Facture;
    }

    public function setFacture(Facture $Facture): static
    {
        // set the owning side of the relation if necessary
        if ($Facture->getListeVente() !== $this) {
            $Facture->setListeVente($this);
        }

        $this->Facture = $Facture;

        return $this;
    }

    /**
     * @return Collection<int, Contenir>
     */
    public function getContenirProduits(): Collection
    {
        return $this->ContenirProduits;
    }

    public function addContenirProduit(Contenir $contenirProduit): static
    {
        if (!$this->ContenirProduits->contains($contenirProduit)) {
            $this->ContenirProduits->add($contenirProduit);
            $contenirProduit->setListeVente($this);
        }

        return $this;
    }

    public function removeContenirProduit(Contenir $contenirProduit): static
    {
        if ($this->ContenirProduits->removeElement($contenirProduit)) {
            // set the owning side to null (unless already changed)
            if ($contenirProduit->getListeVente() === $this) {
                $contenirProduit->setListeVente(null);
            }
        }

        return $this;
    }
}
