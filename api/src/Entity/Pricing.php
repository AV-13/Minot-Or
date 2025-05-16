<?php

namespace App\Entity;

use App\Repository\PricingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PricingRepository::class)]
class Pricing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $fixedFee = null;

    #[ORM\Column]
    private ?\DateTime $modificationDate = null;

    #[ORM\Column]
    private ?float $costPerKm = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFixedFee(): ?float
    {
        return $this->fixedFee;
    }

    public function setFixedFee(float $fixedFee): static
    {
        $this->fixedFee = $fixedFee;

        return $this;
    }

    public function getModificationDate(): ?\DateTime
    {
        return $this->modificationDate;
    }

    public function setModificationDate(\DateTime $modificationDate): static
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    public function getCostPerKm(): ?float
    {
        return $this->costPerKm;
    }

    public function setCostPerKm(float $costPerKm): static
    {
        $this->costPerKm = $costPerKm;

        return $this;
    }
}
