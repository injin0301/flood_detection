<?php

namespace App\EventListener;

use App\Repository\HexTextProtectRepository;
use App\Repository\UtilisateurRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthorizationListener
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private UtilisateurRepository $uRepository,
        private HexTextProtectRepository $hexTextProtectRepository,
    ) {
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getPathInfo() == '/api/login' || $request->getPathInfo() == '/api/register/user' || $request->getPathInfo() == '/api/doc') {
            return;
        }

        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader) {
            throw new AccessDeniedHttpException('L\'en-tête *Authorization* est manquant');
        }

        // strpos($authorizationHeader, 'Bearer: ') !== 0 ||
        if (strpos($authorizationHeader, 'Bearer ') !== 0) {
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
}
