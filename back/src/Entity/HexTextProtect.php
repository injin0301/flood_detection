<?php

namespace App\Entity;

use App\Repository\HexTextProtectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HexTextProtectRepository::class)]
class HexTextProtect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $passPhrase = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassFrase(): ?string
    {
        return '12'; //$this->passPhrase;
    }

    public function setPassFrase(string $passPhrase): static
    {
        $this->passPhrase = $passPhrase;

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
}
