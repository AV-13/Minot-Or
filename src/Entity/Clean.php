<?php

namespace App\Entity;

use App\Repository\CleanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CleanRepository::class)]
class Clean
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'cleans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TruckCleaning $truckCleaning = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'cleans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Truck $truck = null;

    public function getTruckCleaning(): ?TruckCleaning
    {
        return $this->truckCleaning;
    }

    public function setTruckCleaning(?TruckCleaning $truckCleaning): static
    {
        $this->truckCleaning = $truckCleaning;

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
}
