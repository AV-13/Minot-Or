<?php

namespace App\Entity;

use App\Repository\TarificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarificationRepository::class)]
class Tarification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $fraisFixe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\Column]
    private ?float $coutKm = null;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'Tarification', orphanRemoval: true)]
    private Collection $Factures;

    public function __construct()
    {
        $this->Factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFraisFixe(): ?float
    {
        return $this->fraisFixe;
    }

    public function setFraisFixe(float $fraisFixe): static
    {
        $this->fraisFixe = $fraisFixe;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(\DateTimeInterface $dateModif): static
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getCoutKm(): ?float
    {
        return $this->coutKm;
    }

    public function setCoutKm(float $coutKm): static
    {
        $this->coutKm = $coutKm;

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->Factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->Factures->contains($facture)) {
            $this->Factures->add($facture);
            $facture->setTarification($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->Factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getTarification() === $this) {
                $facture->setTarification(null);
            }
        }

        return $this;
    }
}
