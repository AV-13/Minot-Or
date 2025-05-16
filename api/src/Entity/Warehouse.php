<?php

namespace App\Entity;

use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $warehouseAddress = null;

    #[ORM\Column]
    private ?int $storageCapacity = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'warehouse')]
    private Collection $products;

    /**
     * @var Collection<int, Truck>
     */
    #[ORM\OneToMany(targetEntity: Truck::class, mappedBy: 'warehouse')]
    private Collection $trucks;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->trucks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarehouseAddress(): ?string
    {
        return $this->warehouseAddress;
    }

    public function setWarehouseAddress(string $warehouseAddress): static
    {
        $this->warehouseAddress = $warehouseAddress;

        return $this;
    }

    public function getStorageCapacity(): ?int
    {
        return $this->storageCapacity;
    }

    public function setStorageCapacity(int $storageCapacity): static
    {
        $this->storageCapacity = $storageCapacity;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setWarehouse($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getWarehouse() === $this) {
                $product->setWarehouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Truck>
     */
    public function getTrucks(): Collection
    {
        return $this->trucks;
    }

    public function addTruck(Truck $truck): static
    {
        if (!$this->trucks->contains($truck)) {
            $this->trucks->add($truck);
            $truck->setWarehouse($this);
        }

        return $this;
    }

    public function removeTruck(Truck $truck): static
    {
        if ($this->trucks->removeElement($truck)) {
            // set the owning side to null (unless already changed)
            if ($truck->getWarehouse() === $this) {
                $truck->setWarehouse(null);
            }
        }

        return $this;
    }
}
