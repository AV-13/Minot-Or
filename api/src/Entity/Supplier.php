<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $supplierName = null;

    #[ORM\Column(length: 50)]
    private ?string $supplierAddress = null;

    /**
     * @var Collection<int, ProductSupplier>
     */
    #[ORM\OneToMany(targetEntity: ProductSupplier::class, mappedBy: 'supplier')]
    private Collection $productSuppliers;

    /**
     * @var Collection<int, Restock>
     */
    #[ORM\OneToMany(targetEntity: Restock::class, mappedBy: 'supplier')]
    private Collection $restocks;

    public function __construct()
    {
        $this->productSuppliers = new ArrayCollection();
        $this->restocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplierName(): ?string
    {
        return $this->supplierName;
    }

    public function setSupplierName(string $supplierName): static
    {
        $this->supplierName = $supplierName;

        return $this;
    }

    public function getSupplierAddress(): ?string
    {
        return $this->supplierAddress;
    }

    public function setSupplierAddress(string $supplierAddress): static
    {
        $this->supplierAddress = $supplierAddress;

        return $this;
    }

    /**
     * @return Collection<int, ProductSupplier>
     */
    public function getProductSuppliers(): Collection
    {
        return $this->productSuppliers;
    }

    public function addProductSupplier(ProductSupplier $productSupplier): static
    {
        if (!$this->productSuppliers->contains($productSupplier)) {
            $this->productSuppliers->add($productSupplier);
            $productSupplier->setSupplier($this);
        }

        return $this;
    }

    public function removeProductSupplier(ProductSupplier $productSupplier): static
    {
        if ($this->productSuppliers->removeElement($productSupplier)) {
            // set the owning side to null (unless already changed)
            if ($productSupplier->getSupplier() === $this) {
                $productSupplier->setSupplier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Restock>
     */
    public function getRestocks(): Collection
    {
        return $this->restocks;
    }

    public function addRestock(Restock $restock): static
    {
        if (!$this->restocks->contains($restock)) {
            $this->restocks->add($restock);
            $restock->setSupplier($this);
        }

        return $this;
    }

    public function removeRestock(Restock $restock): static
    {
        if ($this->restocks->removeElement($restock)) {
            // set the owning side to null (unless already changed)
            if ($restock->getSupplier() === $this) {
                $restock->setSupplier(null);
            }
        }

        return $this;
    }
}
