<?php

namespace App\Entity;

use App\Repository\ContainsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContainsRepository::class)]
class Contains
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'contains')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SalesList $salesList = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'contains')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $productQuantity = null;

    #[ORM\Column]
    private ?int $productDiscount = null;

    public function getSalesList(): ?SalesList
    {
        return $this->salesList;
    }

    public function setSalesList(?SalesList $salesList): static
    {
        $this->salesList = $salesList;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getProductQuantity(): ?int
    {
        return $this->productQuantity;
    }

    public function setProductQuantity(int $productQuantity): static
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    public function getProductDiscount(): ?int
    {
        return $this->productDiscount;
    }

    public function setProductDiscount(int $productDiscount): static
    {
        $this->productDiscount = $productDiscount;

        return $this;
    }
}
