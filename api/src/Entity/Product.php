<?php

namespace App\Entity;

use App\Enum\ProductCategory;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $productName = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column]
    private ?float $netPrice = null;

    #[ORM\Column]
    private ?float $grossPrice = null;

    #[ORM\Column]
    private ?float $unitWeight = null;

    #[ORM\Column(type: 'product_category_enum', length: 20)]
    private ProductCategory|null $category = null;

    #[ORM\Column]
    private ?int $stockQuantity = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    #[ORM\ManyToMany(targetEntity: Supplier::class, mappedBy: 'products')]
    private Collection $suppliers;

    /**
     * @var Collection<int, Contains>
     */
    #[ORM\OneToMany(targetEntity: Contains::class, mappedBy: 'product')]
    private Collection $contains;

    /**
     * @var Collection<int, ProductSupplier>
     */
    #[ORM\OneToMany(targetEntity: ProductSupplier::class, mappedBy: 'product')]
    private Collection $productSuppliers;

    /**
     * @var Collection<int, Restock>
     */
    #[ORM\OneToMany(targetEntity: Restock::class, mappedBy: 'product')]
    private Collection $restocks;

    public function __construct()
    {
        $this->contains = new ArrayCollection();
        $this->productSuppliers = new ArrayCollection();
        $this->restocks = new ArrayCollection();
        $this->suppliers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getNetPrice(): ?float
    {
        return $this->netPrice;
    }

    public function setNetPrice(float $netPrice): static
    {
        $this->netPrice = $netPrice;

        return $this;
    }

    public function getGrossPrice(): ?float
    {
        return $this->grossPrice;
    }

    public function setGrossPrice(float $grossPrice): static
    {
        $this->grossPrice = $grossPrice;

        return $this;
    }

    public function getUnitWeight(): ?float
    {
        return $this->unitWeight;
    }

    public function setUnitWeight(float $unitWeight): static
    {
        $this->unitWeight = $unitWeight;

        return $this;
    }

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }

    public function setCategory(ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): static
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return Collection<int, Contains>
     */
    public function getContains(): Collection
    {
        return $this->contains;
    }

    public function addContain(Contains $contain): static
    {
        if (!$this->contains->contains($contain)) {
            $this->contains->add($contain);
            $contain->setProduct($this);
        }

        return $this;
    }

    public function removeContain(Contains $contain): static
    {
        if ($this->contains->removeElement($contain)) {
            // set the owning side to null (unless already changed)
            if ($contain->getProduct() === $this) {
                $contain->setProduct(null);
            }
        }

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
            $productSupplier->setProduct($this);
        }

        return $this;
    }

    public function removeProductSupplier(ProductSupplier $productSupplier): static
    {
        if ($this->productSuppliers->removeElement($productSupplier)) {
            // set the owning side to null (unless already changed)
            if ($productSupplier->getProduct() === $this) {
                $productSupplier->setProduct(null);
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
            $restock->setProduct($this);
        }

        return $this;
    }

    public function removeRestock(Restock $restock): static
    {
        if ($this->restocks->removeElement($restock)) {
            // set the owning side to null (unless already changed)
            if ($restock->getProduct() === $this) {
                $restock->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Supplier>
     */
    public function getSuppliers(): Collection
    {
        return $this->suppliers;
    }

    public function addSupplier(Supplier $supplier): static
    {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers->add($supplier);
            $supplier->addProduct($this);
        }
        return $this;
    }

    public function removeSupplier(Supplier $supplier): static
    {
        if ($this->suppliers->removeElement($supplier)) {
            $supplier->removeProduct($this);
        }
        return $this;
    }
}
