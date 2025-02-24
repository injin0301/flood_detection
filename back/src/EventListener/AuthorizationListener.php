<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthorizationListener
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $authorizationHeader = $request->headers->get('Authorization');
        dd($request->headers->all());
        if (!$authorizationHeader) {
            throw new AccessDeniedHttpException('Authorization header is missing');
        }

        if (strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedHttpException('Invalid Authorization header format');
        }

        $token = substr($authorizationHeader, 7);

        try {
            dd($token);
            $parsedToken = $this->jwtManager->parse($token);
            $decodedToken = $this->jwtManager->decode($parsedToken);

            if (!$decodedToken) {
                throw new AccessDeniedHttpException('Invalid JWT token');
            }

            // Ajouter les informations du token à la requête si nécessaire
            $request->attributes->set('jwt_token', $decodedToken);
        } catch (\Exception $e) {
            throw new AccessDeniedHttpException('Invalid JWT token');
        }
    }
}