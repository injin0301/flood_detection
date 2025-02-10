<?php

namespace App\Controller;

use App\Entity\Piece;
use App\Entity\Utilisateur;
use App\Repository\PieceRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
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
                new OA\Property(property: 'csrf_token', type: 'string'),
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
                new OA\Property(property: 'error', type: 'string'),
            ]
        )
    )]
    public function allUtilisateur(
        Request $request,
        UtilisateurRepository $uRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $csrfToken = $data['csrf_token'] ?? '';

        /*if (!$csrfTokenManager->isTokenValid(new CsrfToken('api_token', $csrfToken))) {
            return $this->json(['error' => 'Invalid CSRF Token'], 403);
        }*/

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
                        'description',
                    ],
                ],
            ]),
        ]);
    }

    #[Route('/all/piece', name: '_all_piece', methods: ['GET'])]
    #[OA\RequestBody(
        required: false,
        description: 'Le body contien le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'csrf_token', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste de toute les Piece',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Piece::class))
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'Le CSRF Token n\'est pas valide',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'error', type: 'string'),
            ]
        )
    )]
    public function allPiece(
        Request $request,
        PieceRepository $pRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $csrfToken = $data['csrf_token'] ?? '';

        /*if (!$csrfTokenManager->isTokenValid(new CsrfToken('api_token', $csrfToken))) {
            return $this->json(['error' => 'Invalid CSRF Token'], 403);
        }*/

        return $this->json([
            'piece' => $this->serializer->normalize($pRepository->findAll(), 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'nom',
                    'prenom',
                    'description',
                    'capteur',
                ],
            ]),
        ]);
    }

    #[Route('/piece/{piece<\d*>}/update', name: '_update_piece', methods: ['PUT', 'PATCH'])]
    public function updatePiece(Piece $piece, Request $request): JsonResponse
    {
        return $this->json([]);
    }

    #[Route('/piece/{piece<\d*>}/delete', name: '_delete_piece', methods: ['DELETE'])]
    public function deletePiece(): JsonResponse
    {
        return $this->json([]);
    }

    #[Route('/login', name: '_login', methods: ['POST'])]
    public function login(
        Request $request,
        UtilisateurRepository $uRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
    ): JsonResponse {
        $data = $request->request; // application/x-www-form-urlencoded
        if (!$data->has('email') || !$data->has('password')) {
            return $this->json(['err' => 'Manque de l\'email ou password'], 406);
        }

        $verif = $uRepository->findOneBy(['email' => $data->get('email')]);

        if (empty($verif) || !$passwordHasher->isPasswordValid($verif, $data->getString('password'))) {
            return $this->json(['err' => 'Utilisateur non trouvé'], 404);
        }

        return $this->json(['token' => $jwtManager->create($verif)]);
    }

    #[Route('/register/user', name: '_register_user', methods: ['POST'])]
    public function makeUser(
        Request $request,
        UtilisateurRepository $uRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $data = $request->request; // application/x-www-form-urlencoded

        if (!$data->has(key: 'email') || !$data->has('password')) {
            return $this->json(['err' => 'Manque de l\'email ou password'], 406);
        }

        $userEmail = $uRepository->findOneBy(['email' => $data->getString('email')]);

        if (!empty($userEmail)) {
            return $this->json(['err' => 'Il existe déjà utilisateur avec cette email'], 409);
        }

        $user = new Utilisateur();
        $user->setEmail($data->getString('email'));

        $has = $passwordHasher->hashPassword($user, $data->getString('password'));
        // * ^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$
        $user->setPassword($has);
        $user->setNom('');
        $user->setPrenom('');
        $user->setCity('');
        $user->setTel(0);
        $user->setZipCode('');

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Utilisateur crée'], 201);
    }

    #[Route('/piece/create', name: '_add_piece', methods: ['POST'])]
    public function creePiece(Request $request, PieceRepository $pRepository, EntityManagerInterface $em): JsonResponse
    {
        return $this->json([]);
    }
}
