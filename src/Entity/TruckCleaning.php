<?php

namespace App\Entity;

use App\Repository\TruckCleaningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TruckCleaningRepository::class)]
class TruckCleaning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $cleaningStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $cleaningEndDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $observations = null;

    /**
     * @var Collection<int, Clean>
     */
    #[ORM\OneToMany(targetEntity: Clean::class, mappedBy: 'truckCleaning')]
    private Collection $cleans;

    public function __construct()
    {
        $this->cleans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCleaningStartDate(): ?\DateTime
    {
        return $this->cleaningStartDate;
    }

    public function setCleaningStartDate(\DateTime $cleaningStartDate): static
    {
        $this->cleaningStartDate = $cleaningStartDate;

        return $this;
    }

    public function getCleaningEndDate(): ?\DateTime
    {
        return $this->cleaningEndDate;
    }

    public function setCleaningEndDate(\DateTime $cleaningEndDate): static
    {
        $this->cleaningEndDate = $cleaningEndDate;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(string $observations): static
    {
        $this->observations = $observations;

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
            $clean->setTruckCleaning($this);
        }

        return $this;
    }

    public function removeClean(Clean $clean): static
    {
        if ($this->cleans->removeElement($clean)) {
            // set the owning side to null (unless already changed)
            if ($clean->getTruckCleaning() === $this) {
                $clean->setTruckCleaning(null);
            }
        }

        return $this;
    }
}
