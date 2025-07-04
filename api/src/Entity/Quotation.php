<?php

namespace App\Entity;

use App\Repository\QuotationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuotationRepository::class)]
class Quotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $issueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dueDate = null;

    #[ORM\Column]
    private ?bool $paymentStatus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $acceptanceDate = null;

    #[ORM\OneToOne(inversedBy: 'quotations', cascade: ['persist', 'remove'])]
    private ?SalesList $salesList = null;
    #[ORM\ManyToOne(targetEntity: Pricing::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pricing $pricing = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getIssueDate(): ?\DateTime
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTime $issueDate): static
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function isPaymentStatus(): ?bool
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(bool $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getAcceptanceDate(): ?\DateTime
    {
        return $this->acceptanceDate;
    }

    public function setAcceptanceDate(\DateTime $acceptanceDate): static
    {
        $this->acceptanceDate = $acceptanceDate;

        return $this;
    }

    public function getSalesList(): ?SalesList
    {
        return $this->salesList;
    }

    public function setSalesList(?SalesList $salesList): static
    {
        $this->salesList = $salesList;

        return $this;
    }
    public function getPricing(): ?Pricing
    {
        return $this->pricing;
    }

    public function setPricing(?Pricing $pricing): static
    {
        $this->pricing = $pricing;
        return $this;
    }
}
