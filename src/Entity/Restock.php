<?php

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Repository\RestockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestockRepository::class)]
class Restock
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'restocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'restocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Truck $truck = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'restocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $supplierProductQuantity = null;

    #[ORM\Column(length: 20)]
    private ?string $orderNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $orderDate = null;

    #[ORM\Column(length: 50)]
    private ?OrderStatus $orderStatus = null;

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getTruck(): ?Truck
    {
        return $this->truck;
    }

    public function setTruck(?Truck $truck): static
    {
        $this->truck = $truck;

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

    public function getSupplierProductQuantity(): ?int
    {
        return $this->supplierProductQuantity;
    }

    public function setSupplierProductQuantity(int $supplierProductQuantity): static
    {
        $this->supplierProductQuantity = $supplierProductQuantity;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getOrderDate(): ?\DateTime
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTime $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(OrderStatus $orderStatus): self
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }
}
