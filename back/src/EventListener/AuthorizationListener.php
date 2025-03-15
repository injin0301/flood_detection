<?php

namespace App\EventListener;

use App\Entity\Capteur;
use App\Repository\HexTextProtectRepository;
use App\Repository\PieceRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthorizationListener
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private UtilisateurRepository $uRepository,
        private PieceRepository $pRepository,
        private EntityManagerInterface $em,
        private HexTextProtectRepository $hexTextProtectRepository,
        private HttpClientInterface $client,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (in_array($request->getPathInfo(), ['/api/login', '/api/enregistrer/utilisateur', '/api/doc'])) {
            return;
        }

        $this->gerRecupData();

        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader) {
            throw new AccessDeniedHttpException('L\'en-tête *Authorization* est manquant');
        }

        // strpos($authorizationHeader, 'Bearer: ') !== 0 ||
        if (0 !== strpos($authorizationHeader, 'Bearer ')) {
            throw new AccessDeniedHttpException('Format de l\'en-tête *Authorization* invalide');
        }

        if (preg_match('/Bearer /', $authorizationHeader)) {
            $token = substr($authorizationHeader, 7);
        }

        try {
            $parsedToken = $this->jwtManager->parse($token);

            if (empty($parsedToken)) {
                throw new AccessDeniedHttpException('Jeton JWT invalide');
            }

            $utilisateur = $this->uRepository->findOneBy(['email' => $parsedToken['username']]);
            $text = $this->hexTextProtectRepository->findOneBy(['utilisateur' => $utilisateur->getId()]);

            if (empty($text)) {
                throw new AccessDeniedHttpException('Jeton JWT invalide (utilisateur incorrect)');
            }

            if ($text->getPassFrase() !== $parsedToken['passphrase']) {
                throw new AccessDeniedHttpException('Jeton JWT invalide (passphrase incorrect)');
            }

            // Ajouter les informations du token à la requête si nécessaire
            $request->attributes->set('jwt_token', $parsedToken);
        } catch (\Exception $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }

    public function gerRecupData(): void
    {
        $pieces = $this->pRepository->findAll();
        if (empty($pieces)) {
            return;
        }

        $response = $this->client->request('GET', 'https://api.tutiempo.net/json/?lan=fr&apid=zwDX4azaz4X4Xqs&ll=40.4178,-3.7022');
        if (200 !== $response->getStatusCode()) {
            return;
        }

        $data = $response->toArray();
        foreach ($pieces as $piece) {
            foreach (['day1', 'day2', 'day3', 'day4', 'day5', 'day6', 'day7'] as $day) {
                $capteur = new Capteur();
                $capteur->setHumidite($data[$day]['humidity']);
                $capteur->setTemperature($data[$day]['temperature_max']);
                $capteur->setNiveauEau($data[$day]['icon']);
                $capteur->setInondation($data[$day]['icon'] > 20 && $data[$day]['humidity'] > 80);
                $this->em->persist($capteur);
                $piece->addCapteur($capteur);
                $this->em->persist($piece);
                $this->em->flush();
            }
        }
    }
}
