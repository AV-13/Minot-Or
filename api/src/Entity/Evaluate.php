<?php

namespace App\Entity;

use App\Repository\EvaluateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluateRepository::class)]
class Evaluate
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'evaluates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SalesList $salesList = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'evaluates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reviewer = null;

    #[ORM\Column]
    private ?bool $quoteAccepted = null;

    public function getSalesList(): ?SalesList
    {
        return $this->salesList;
    }

    public function setSalesList(?SalesList $salesList): static
    {
        $this->salesList = $salesList;

        return $this;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?User $reviewer): static
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function isQuoteAccepted(): ?bool
    {
        return $this->quoteAccepted;
    }

    public function setQuoteAccepted(bool $quoteAccepted): static
    {
        $this->quoteAccepted = $quoteAccepted;

        return $this;
    }
}
