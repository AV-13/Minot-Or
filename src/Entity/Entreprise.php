<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomEntreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $siretEntreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $coordonneeEntreprise = null;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\OneToMany(targetEntity: Utilisateur::class, mappedBy: 'entreprise', orphanRemoval: true)]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): static
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getSiretEntreprise(): ?string
    {
        return $this->siretEntreprise;
    }

    public function setSiretEntreprise(string $siretEntreprise): static
    {
        $this->siretEntreprise = $siretEntreprise;

        return $this;
    }

    public function getCoordonneeEntreprise(): ?string
    {
        return $this->coordonneeEntreprise;
    }

    public function setCoordonneeEntreprise(string $coordonneeEntreprise): static
    {
        $this->coordonneeEntreprise = $coordonneeEntreprise;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setEntreprise($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getEntreprise() === $this) {
                $utilisateur->setEntreprise(null);
            }
        }

        return $this;
    }
}
