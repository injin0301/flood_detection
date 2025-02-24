<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('api/csrf-token', name: 'generate_token')]
#[Security(name: 'BearerAuth')]
final class CSRFTokenController extends AbstractController
{
    #[Route('/', name: '_init', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Route Pour générer le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'csrf_token', type: 'string')
            ]
        )
    )]
    public function index(Request $request, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $token = $csrfTokenManager->getToken('api_token')->getValue();

        return $this->json([
            'csrf_token' => $token,
        ]);
    }

    #[Route('/protected-action', name: '_protected-action', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        description: 'Le body doit contenir le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'csrf_token', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Permet de validé le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'Le CSRF Token n\'est pas valide',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'error', type: 'string')
            ]
        )
    )]
    public function protectedAction(Request $request, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $csrfToken = $data['csrf_token'] ?? '';

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('api_token', $csrfToken))) {
            return $this->json(['error' => 'Invalid CSRF Token'], 403);
        }

        return $this->json(['message' => 'success!']);
    }
}
