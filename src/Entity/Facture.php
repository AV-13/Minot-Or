<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montantTotal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEmission = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEcheance = null;

    #[ORM\Column]
    private ?bool $statutPaiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAcceptation = null;

    #[ORM\OneToOne(inversedBy: 'Facture', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ListeVente $ListeVente = null;

    #[ORM\ManyToOne(inversedBy: 'Factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tarification $Tarification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

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

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(\DateTimeInterface $dateEcheance): static
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    public function isStatutPaiement(): ?bool
    {
        return $this->statutPaiement;
    }

    public function setStatutPaiement(bool $statutPaiement): static
    {
        $this->statutPaiement = $statutPaiement;

        return $this;
    }

    public function getDateAcceptation(): ?\DateTimeInterface
    {
        return $this->dateAcceptation;
    }

    public function setDateAcceptation(?\DateTimeInterface $dateAcceptation): static
    {
        $this->dateAcceptation = $dateAcceptation;

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

    public function getTarification(): ?Tarification
    {
        return $this->Tarification;
    }

    public function setTarification(?Tarification $Tarification): static
    {
        $this->Tarification = $Tarification;

        return $this;
    }
}
