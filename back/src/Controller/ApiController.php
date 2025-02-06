<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/api', name: 'app_api')]
final class ApiController extends AbstractController
{
    private Serializer $serializer;

    public function __construct()
    {
        $normalizer = [new ObjectNormalizer()];
        $encoding = [new JsonEncoder(), new XmlEncoder(), new CsvEncoder(), new YamlEncoder()];

        $this->serializer = new Serializer($normalizer, $encoding);
    }

    #[Route('/all/utilisateurs', name: '_all_utilisateur', methods: ['GET'])]
    #[OA\RequestBody(
        required: false,
        description: 'Le body contien le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'csrf_token', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste de tout les Utilisateur',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Utilisateur::class))
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
    public function allUtilisateur(
        Request $request,
        UtilisateurRepository $uRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $csrfToken = $data['csrf_token'] ?? '';

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('api_token', $csrfToken))) {
            return $this->json(['error' => 'Invalid CSRF Token'], 403);
        }

        return $this->json([
            'utilisateur' => $this->serializer->normalize($uRepository->findAll(), 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'email',
                    'roles',
                    'nom',
                    'prenom',
                    'tel',
                    'city',
                    'zipCode',
                    'piece' => [
                        'id',
                        'nom',
                        'description'
                    ],
                ],
            ])
        ]);
    }

    #[Route('/login', name: '_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = $request->request; //application/x-www-form-urlencoded

        if (!$data->has('email') || !$data->has('password')) {
            return $this->json(['err' => 'Manque de l\'email ou password']);
        }

        dd($data);

        return $this->json([]);
    }
}
