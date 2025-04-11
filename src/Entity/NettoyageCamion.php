<?php

namespace App\Entity;

use App\Repository\NettoyageCamionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NettoyageCamionRepository::class)]
class NettoyageCamion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebutNettoyage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinNettoyage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutNettoyage(): ?\DateTimeInterface
    {
        return $this->dateDebutNettoyage;
    }

    public function setDateDebutNettoyage(\DateTimeInterface $dateDebutNettoyage): static
    {
        $this->dateDebutNettoyage = $dateDebutNettoyage;

        return $this;
    }

    public function getDateFinNettoyage(): ?\DateTimeInterface
    {
        return $this->dateFinNettoyage;
    }

    public function setDateFinNettoyage(\DateTimeInterface $dateFinNettoyage): static
    {
        $this->dateFinNettoyage = $dateFinNettoyage;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): static
    {
        $this->observations = $observations;

        return $this;
    }
}
