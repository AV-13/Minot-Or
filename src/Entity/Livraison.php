<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroLivraison = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarqueLivreur = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $qrCode = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ListeVente $ListeVente = null;

    #[ORM\ManyToOne(inversedBy: 'Livraison')]
    private ?Camion $Camion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(\DateTimeInterface $dateLivraison): static
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getNumeroLivraison(): ?string
    {
        return $this->numeroLivraison;
    }

    public function setNumeroLivraison(string $numeroLivraison): static
    {
        $this->numeroLivraison = $numeroLivraison;

        return $this;
    }

    public function getRemarqueLivreur(): ?string
    {
        return $this->remarqueLivreur;
    }

    public function setRemarqueLivreur(?string $remarqueLivreur): static
    {
        $this->remarqueLivreur = $remarqueLivreur;

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(string $qrCode): static
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function getListeVente(): ?ListeVente
    {
        return $this->ListeVente;
    }

    public function setListeVente(ListeVente $ListeVente): static
    {
        $this->ListeVente = $ListeVente;

        return $this;
    }

    public function getCamion(): ?Camion
    {
        return $this->Camion;
    }

    public function setCamion(?Camion $Camion): static
    {
        $this->Camion = $Camion;

        return $this;
    }
}
