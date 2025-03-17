<?php

namespace App\Service;

use App\Entity\HexTextProtect;
use App\Entity\Utilisateur;
use App\Repository\HexTextProtectRepository;
use Doctrine\ORM\EntityManagerInterface;

class HexTextService
{
    private Utilisateur $utilisateur;

    public function __construct(
        private EntityManagerInterface $em,
        private HexTextProtectRepository $htpRepository
    ) {
    }

    /**
     * Définit l'utilisateur pour le service.
     *
     * @param Utilisateur $utilisateur L'utilisateur à définir.
     */
    public function setUtilisateur(Utilisateur $utilisateur): void
    {
        $this->utilisateur = $utilisateur;
    }

    /**
     * Génère un texte hexadécimal aléatoire.
     *
     * @param int $length La longueur du texte hexadécimal à générer.
     *
     * @return string Le texte hexadécimal généré.
     */
    public function generateHexText(int $length = 16): string
    {
        $characters = '0123456789abcdef';
        $hexText = '';

        for ($i = 0; $i < $length; $i++) {
            $hexText .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $hexText;
    }

    /**
     * Sauvegarde le texte hexadécimal pour l'utilisateur.
     *
     * @param string $hexText Le texte hexadécimal à sauvegarder.
     *
     * @return HexTextProtect L'entité HexTextProtect sauvegardée.
     */
    public function saveHexText(string $hexText): HexTextProtect
    {
        // Vérifiez si une entité HexTextProtect existe déjà pour l'utilisateur
        $hexTextEntity = $this->htpRepository->findOneBy(['utilisateur' => $this->utilisateur]);

        if (!$hexTextEntity) {
            // Si l'entité n'existe pas, créez une nouvelle entité
            $hexTextEntity = new HexTextProtect();
            $hexTextEntity->setUtilisateur($this->utilisateur);
        }

        // Mettez à jour la valeur de passFrase
        $hexTextEntity->setPassFrase($hexText);

        // Persistez et sauvegardez l'entité
        $this->em->persist($hexTextEntity);
        $this->em->flush();

        return $hexTextEntity;
    }
}
