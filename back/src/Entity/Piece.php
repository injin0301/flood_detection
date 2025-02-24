<?php

namespace App\Entity;

use App\Repository\PieceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PieceRepository::class)]
class Piece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'piece')]
    private ?Utilisateur $utilisateur = null;

    /**
     * @var Collection<int, Capteur>
     */
    #[ORM\OneToMany(targetEntity: Capteur::class, mappedBy: 'piece')]
    private Collection $capteur;

    public function __construct()
    {
        $this->capteur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection<int, Capteur>
     */
    public function getCapteur(): Collection
    {
        return $this->capteur;
    }

    public function addCapteur(Capteur $capteur): static
    {
        if (!$this->capteur->contains($capteur)) {
            $this->capteur->add($capteur);
            $capteur->setPiece($this);
        }

        return $this;
    }

    public function removeCapteur(Capteur $capteur): static
    {
        if ($this->capteur->removeElement($capteur)) {
            // set the owning side to null (unless already changed)
            if ($capteur->getPiece() === $this) {
                $capteur->setPiece(null);
            }
        }

        return $this;
    }
}
