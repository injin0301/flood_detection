<?php

namespace App\Service;

use App\Entity\HexTextProtect;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class HexTextService
{
    private Utilisateur $utilisateur;
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function setUtilisateur(Utilisateur $utilisateur): void
    {
        $this->utilisateur = $utilisateur;
    }

    public function generateHexText(int $length = 16): string
    {
        $characters = '0123456789abcdef';
        $hexText = '';

        for ($i = 0; $i < $length; $i++) {
            $hexText .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $hexText;
    }

    public function saveHexText(string $hexText): HexTextProtect
    {
        $hexTextEntity = new HexTextProtect();
        $hexTextEntity->setPassFrase($hexText);
        $hexTextEntity->setUtilisateur($this->utilisateur);

        $this->em->persist($hexTextEntity);
        $this->em->flush();

        return $hexTextEntity;
    }
}
