<?php

namespace App\Entity;

use App\Enum\TruckCategory;
use App\Repository\TruckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TruckRepository::class)]
class Truck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $registrationNumber = null;

    #[ORM\Column(length: 50)]
    private TruckCategory|null $truckType = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\Column]
    private ?int $deliveryCount = null;

    #[ORM\Column]
    private ?float $transportDistance = null;

    #[ORM\Column]
    private ?float $transportFee = null;

    #[ORM\ManyToOne(inversedBy: 'trucks')]
    private ?Delivery $delivery = null;

    #[ORM\ManyToOne(inversedBy: 'trucks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    #[ORM\ManyToOne(inversedBy: 'trucks')]
    private ?User $driver = null;

    /**
     * @var Collection<int, Restock>
     */
    #[ORM\OneToMany(targetEntity: Restock::class, mappedBy: 'truck')]
    private Collection $restocks;

    /**
     * @var Collection<int, Clean>
     */
    #[ORM\OneToMany(targetEntity: Clean::class, mappedBy: 'truck')]
    private Collection $cleans;

    public function __construct()
    {
        $this->restocks = new ArrayCollection();
        $this->cleans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): static
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getTruckType(): ?TruckCategory
    {
        return $this->truckType;
    }

    public function setTruckType(TruckCategory $truckType): static
    {
        $this->truckType = $truckType;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getDeliveryCount(): ?int
    {
        return $this->deliveryCount;
    }

    public function setDeliveryCount(int $deliveryCount): static
    {
        $this->deliveryCount = $deliveryCount;

        return $this;
    }

    public function getTransportDistance(): ?float
    {
        return $this->transportDistance;
    }

    public function setTransportDistance(float $transportDistance): static
    {
        $this->transportDistance = $transportDistance;

        return $this;
    }

    public function getTransportFee(): ?float
    {
        return $this->transportFee;
    }

    public function setTransportFee(float $transportFee): static
    {
        $this->transportFee = $transportFee;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): static
    {
        $this->delivery = $delivery;

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

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): static
    {
        $this->driver = $driver;

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
            $restock->setTruck($this);
        }

        return $this;
    }

    public function removeRestock(Restock $restock): static
    {
        if ($this->restocks->removeElement($restock)) {
            // set the owning side to null (unless already changed)
            if ($restock->getTruck() === $this) {
                $restock->setTruck(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Clean>
     */
    public function getCleans(): Collection
    {
        return $this->cleans;
    }

    public function addClean(Clean $clean): static
    {
        if (!$this->cleans->contains($clean)) {
            $this->cleans->add($clean);
            $clean->setTruck($this);
        }

        return $this;
    }

    public function removeClean(Clean $clean): static
    {
        if ($this->cleans->removeElement($clean)) {
            // set the owning side to null (unless already changed)
            if ($clean->getTruck() === $this) {
                $clean->setTruck(null);
            }
        }

        return $this;
    }
}
