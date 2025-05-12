<?php

namespace App\Entity;

use App\Enum\SalesStatus;
use App\Repository\SalesListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalesListRepository::class)]
class SalesList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private SalesStatus|null $status;

    #[ORM\Column]
    private ?float $productsPrice = null;

    #[ORM\Column]
    private ?int $globalDiscount = null;

    #[ORM\Column]
    private ?\DateTime $issueDate = null;

    #[ORM\Column]
    private ?\DateTime $expirationDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $orderDate = null;

    #[ORM\OneToOne(mappedBy: 'salesList', cascade: ['persist', 'remove'])]
    private ?Delivery $delivery = null;

    #[ORM\OneToOne(mappedBy: 'salesList', cascade: ['persist', 'remove'])]
    private ?Invoice $invoices = null;

    /**
     * @var Collection<int, Contains>
     */
    #[ORM\OneToMany(targetEntity: Contains::class, mappedBy: 'salesList')]
    private Collection $contains;

    /**
     * @var Collection<int, Evaluate>
     */
    #[ORM\OneToMany(targetEntity: Evaluate::class, mappedBy: 'salesList')]
    private Collection $evaluates;

    public function __construct()
    {
        $this->contains = new ArrayCollection();
        $this->evaluates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?SalesStatus
    {
        return $this->status;
    }

    public function setStatus(SalesStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProductsPrice(): ?float
    {
        return $this->productsPrice;
    }

    public function setProductsPrice(float $productsPrice): static
    {
        $this->productsPrice = $productsPrice;

        return $this;
    }

    public function getGlobalDiscount(): ?int
    {
        return $this->globalDiscount;
    }

    public function setGlobalDiscount(int $globalDiscount): static
    {
        $this->globalDiscount = $globalDiscount;

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

    public function getExpirationDate(): ?\DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTime $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getOrderDate(): ?\DateTime
    {
        return $this->orderDate;
    }

    public function setOrderDate(?\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(Delivery $delivery): static
    {
        // set the owning side of the relation if necessary
        if ($delivery->getSalesList() !== $this) {
            $delivery->setSalesList($this);
        }

        $this->delivery = $delivery;

        return $this;
    }

    public function getInvoices(): ?Invoice
    {
        return $this->invoices;
    }

    public function setInvoices(?Invoice $invoices): static
    {
        // unset the owning side of the relation if necessary
        if ($invoices === null && $this->invoices !== null) {
            $this->invoices->setSalesList(null);
        }

        // set the owning side of the relation if necessary
        if ($invoices !== null && $invoices->getSalesList() !== $this) {
            $invoices->setSalesList($this);
        }

        $this->invoices = $invoices;

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
            $contain->setSalesList($this);
        }

        return $this;
    }

    public function removeContain(Contains $contain): static
    {
        if ($this->contains->removeElement($contain)) {
            // set the owning side to null (unless already changed)
            if ($contain->getSalesList() === $this) {
                $contain->setSalesList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evaluate>
     */
    public function getEvaluates(): Collection
    {
        return $this->evaluates;
    }

    public function addEvaluate(Evaluate $evaluate): static
    {
        if (!$this->evaluates->contains($evaluate)) {
            $this->evaluates->add($evaluate);
            $evaluate->setSalesList($this);
        }

        return $this;
    }

    public function removeEvaluate(Evaluate $evaluate): static
    {
        if ($this->evaluates->removeElement($evaluate)) {
            // set the owning side to null (unless already changed)
            if ($evaluate->getSalesList() === $this) {
                $evaluate->setSalesList(null);
            }
        }

        return $this;
    }
}
