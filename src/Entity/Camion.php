<?php

namespace App\Entity;

use App\Repository\CamionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CamionRepository::class)]
class Camion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $immatriculationCamion = null;

    #[ORM\Column]
    private ?bool $disponibleCamion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dernierNettoyage = null;

    #[ORM\Column]
    private ?int $nombreDeLivraison = null;

    #[ORM\Column]
    private ?float $distanceTransport = null;

    /**
     * @var Collection<int, Livraison>
     */
    #[ORM\OneToMany(targetEntity: Livraison::class, mappedBy: 'Camion')]
    private Collection $Livraison;

    #[ORM\ManyToOne(inversedBy: 'Camions')]
    private ?Entrepot $Entrepot = null;

    /**
     * @var Collection<int, Utilisateur>
     */
    #[ORM\ManyToMany(targetEntity: Utilisateur::class)]
    private Collection $Utilisateur;

    public function __construct()
    {
        $this->Livraison = new ArrayCollection();
        $this->Utilisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatriculationCamion(): ?string
    {
        return $this->immatriculationCamion;
    }

    public function setImmatriculationCamion(string $immatriculationCamion): static
    {
        $this->immatriculationCamion = $immatriculationCamion;

        return $this;
    }

    public function isDisponibleCamion(): ?bool
    {
        return $this->disponibleCamion;
    }

    public function setDisponibleCamion(bool $disponibleCamion): static
    {
        $this->disponibleCamion = $disponibleCamion;

        return $this;
    }

    public function getDernierNettoyage(): ?\DateTimeInterface
    {
        return $this->dernierNettoyage;
    }

    public function setDernierNettoyage(\DateTimeInterface $dernierNettoyage): static
    {
        $this->dernierNettoyage = $dernierNettoyage;

        return $this;
    }

    public function getNombreDeLivraison(): ?int
    {
        return $this->nombreDeLivraison;
    }

    public function setNombreDeLivraison(int $nombreDeLivraison): static
    {
        $this->nombreDeLivraison = $nombreDeLivraison;

        return $this;
    }

    public function getDistanceTransport(): ?float
    {
        return $this->distanceTransport;
    }

    public function setDistanceTransport(float $distanceTransport): static
    {
        $this->distanceTransport = $distanceTransport;

        return $this;
    }

    /**
     * @return Collection<int, Livraison>
     */
    public function getLivraison(): Collection
    {
        return $this->Livraison;
    }

    public function addLivraison(Livraison $livraison): static
    {
        if (!$this->Livraison->contains($livraison)) {
            $this->Livraison->add($livraison);
            $livraison->setCamion($this);
        }

        return $this;
    }

    public function removeLivraison(Livraison $livraison): static
    {
        if ($this->Livraison->removeElement($livraison)) {
            // set the owning side to null (unless already changed)
            if ($livraison->getCamion() === $this) {
                $livraison->setCamion(null);
            }
        }

        return $this;
    }

    public function getEntrepot(): ?Entrepot
    {
        return $this->Entrepot;
    }

    public function setEntrepot(?Entrepot $Entrepot): static
    {
        $this->Entrepot = $Entrepot;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateur(): Collection
    {
        return $this->Utilisateur;
    }

    public function addUtilisateur(Utilisateur $utilisateur): static
    {
        if (!$this->Utilisateur->contains($utilisateur)) {
            $this->Utilisateur->add($utilisateur);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        $this->Utilisateur->removeElement($utilisateur);

        return $this;
    }
}
