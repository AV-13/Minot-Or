<?php

namespace App\Entity;

use App\Repository\EvaluerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluerRepository::class)]
class Evaluer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $devisAccepter = null;

    #[ORM\ManyToOne(inversedBy: 'EvaluationsDevis')]
    private ?Utilisateur $Utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isDevisAccepter(): ?bool
    {
        return $this->devisAccepter;
    }

    public function setDevisAccepter(bool $devisAccepter): static
    {
        $this->devisAccepter = $devisAccepter;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?Utilisateur $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }
}
