<?php

namespace App\Entity;

use App\Repository\CapteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CapteurRepository::class)]
class Capteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $humidite = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?float $niveau_eau = null;

    #[ORM\ManyToOne(inversedBy: 'capteur')]
    private ?Piece $piece = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHumidite(): ?float
    {
        return $this->humidite;
    }

    public function setHumidite(float $humidite): static
    {
        $this->humidite = $humidite;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getNiveauEau(): ?float
    {
        return $this->niveau_eau;
    }

    public function setNiveauEau(float $niveau_eau): static
    {
        $this->niveau_eau = $niveau_eau;

        return $this;
    }

    public function getPiece(): ?Piece
    {
        return $this->piece;
    }

    public function setPiece(?Piece $piece): static
    {
        $this->piece = $piece;

        return $this;
    }
}
