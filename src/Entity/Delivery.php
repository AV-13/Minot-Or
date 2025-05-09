<?php

namespace App\Entity;

use App\Enum\DeliveryStatus;
use App\Repository\DeliveryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeliveryRepository::class)]
class Delivery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $deliveryDate = null;

    #[ORM\Column(length: 50)]
    private ?string $deliveryAddress = null;

    #[ORM\Column(length: 20)]
    private ?string $deliveryNumber = null;

    #[ORM\Column(length: 20)]
    private DeliveryStatus|null $deliveryStatus = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $driverRemark = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $qrCode = null;

    #[ORM\OneToOne(inversedBy: 'delivery', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SalesList $salesList = null;

    /**
     * @var Collection<int, Truck>
     */
    #[ORM\OneToMany(targetEntity: Truck::class, mappedBy: 'delivery')]
    private Collection $trucks;

    public function __construct()
    {
        $this->trucks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryDate(): ?\DateTime
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTime $deliveryDate): static
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getDeliveryNumber(): ?string
    {
        return $this->deliveryNumber;
    }

    public function setDeliveryNumber(string $deliveryNumber): static
    {
        $this->deliveryNumber = $deliveryNumber;

        return $this;
    }

    public function getDeliveryStatus(): ?DeliveryStatus
    {
        return $this->deliveryStatus;
    }

    public function setDeliveryStatus(DeliveryStatus $deliveryStatus): self
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    public function getDriverRemark(): ?string
    {
        return $this->driverRemark;
    }

    public function setDriverRemark(string $driverRemark): static
    {
        $this->driverRemark = $driverRemark;

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(string $qrCode): static
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function getSalesList(): ?SalesList
    {
        return $this->salesList;
    }

    public function setSalesList(SalesList $salesList): static
    {
        $this->salesList = $salesList;

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
            $truck->setDelivery($this);
        }

        return $this;
    }

    public function removeTruck(Truck $truck): static
    {
        if ($this->trucks->removeElement($truck)) {
            // set the owning side to null (unless already changed)
            if ($truck->getDelivery() === $this) {
                $truck->setDelivery(null);
            }
        }

        return $this;
    }
}
