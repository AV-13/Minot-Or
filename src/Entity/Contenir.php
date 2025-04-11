<?php

namespace App\Entity;

use App\Repository\ContenirRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenirRepository::class)]
class Contenir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantiteProduit = null;

    #[ORM\Column]
    private ?int $reductionProduit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $Produit = null;

    #[ORM\ManyToOne(inversedBy: 'ContenirProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ListeVente $ListeVente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteProduit(): ?int
    {
        return $this->quantiteProduit;
    }

    public function setQuantiteProduit(int $quantiteProduit): static
    {
        $this->quantiteProduit = $quantiteProduit;

        return $this;
    }

    public function getReductionProduit(): ?int
    {
        return $this->reductionProduit;
    }

    public function setReductionProduit(int $reductionProduit): static
    {
        $this->reductionProduit = $reductionProduit;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->Produit;
    }

    public function setProduit(?Produit $Produit): static
    {
        $this->Produit = $Produit;

        return $this;
    }

    public function getListeVente(): ?ListeVente
    {
        return $this->ListeVente;
    }

    public function setListeVente(?ListeVente $ListeVente): static
    {
        $this->ListeVente = $ListeVente;

        return $this;
    }
}
