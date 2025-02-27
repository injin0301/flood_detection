<?php

namespace App\Controller;

use App\Entity\Piece;
use App\Entity\Utilisateur;
use App\Repository\HexTextProtectRepository;
use App\Repository\PieceRepository;
use App\Repository\UtilisateurRepository;
use App\Service\HexTextService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
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
    #[Security(name: 'BearerAuth')]
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
    #[Security(name: 'BearerAuth')]
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
    #[Security(name: 'BearerAuth')]
    #[OA\RequestBody(
        required: true,
        description: 'les informations de la pièce à mettre à jour pour PATCH, ce n\'est pas obligatoire de tout mettre',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'idUtilisateur', type: 'integer'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Piece modifié avec succès',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 406,
        description: 'Manque de données requises',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    public function updatePiece(
        Piece $piece,
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $uRepository
    ): JsonResponse {
        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));
        if ($request->isMethod(Request::METHOD_PUT)) {
            foreach (['description', 'nom', 'idUtilisateur'] as $value) {
                if (!$data->has($value)) {
                    return $this->json(['err' => "Manque de {$value}"], 406);
                }
            }
            $piece->setDescription($data->getString('description'));
            $piece->setNom($data->getString('nom'));
            $piece->setUtilisateur($uRepository->find($data->getInt('idUtilisateur')));

            $em->persist($piece);
            $em->flush();
        } elseif ($request->isMethod(Request::METHOD_PATCH)) {
            if ($data->has('description')) {
                $piece->setDescription($data->getString('description'));
            }
            if ($data->has('nom')) {
                $piece->setNom($data->getString('nom'));
            }
            if ($data->has('idUtilisateur')) {
                $piece->setUtilisateur($uRepository->find($data->getInt('idUtilisateur')));
            }

            $em->persist($piece);
            $em->flush();
        }

        return $this->json(['message' => 'Piece modifié'], 200);
    }

    #[Route('/piece/{piece<\d*>}/delete', name: '_delete_piece', methods: ['DELETE'])]
    #[Security(name: 'BearerAuth')]
    #[OA\RequestBody(
        required: false,
        description: 'Pour supprimer une pièce',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'piece', type: 'integer'),
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Piece supprimée avec succès',
    )]
    #[OA\Response(
        response: 404,
        description: 'Piece non trouvée',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'error', type: 'string'),
            ]
        )
    )]
    public function deletePiece(Piece $piece, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($piece);
        $em->flush();
        return $this->json([], 204);
    }

    #[Route('/login', name: '_login', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        description: 'Pour se connecter',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Utilisateur non trouvé',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 406,
        description: 'Manque de l\'email ou password',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    public function login(
        Request $request,
        UtilisateurRepository $uRepository,
        HexTextProtectRepository $htRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        HexTextService $hexTextService,
    ): JsonResponse {
        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));
        if (!$data->has('email') || !$data->has('password')) {
            return $this->json(['err' => 'Manque de l\'email ou password'], 406);
        }

        $verif = $uRepository->findOneBy(['email' => $data->get('email')]);

        if (empty($verif) || !$passwordHasher->isPasswordValid($verif, $data->getString('password'))) {
            return $this->json(['err' => 'Utilisateur non trouvé'], 404);
        }
        $hexTextService->setUtilisateur($verif);
        $hexText = $hexTextService->generateHexText();
        $hexTextEntity = $hexTextService->saveHexText($hexText);

        return $this->json([
            'token' => $jwtManager->createFromPayload(
                $verif,
                [
                    'passphrase' => $hexTextEntity->getPassFrase()
                ]
            )
        ]);
    }

    #[Route('/register/user', name: '_register_user', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        description: 'Pour crée un utilisateur',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Utilisateur créé avec succès',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 406,
        description: 'Manque de l\'email ou password',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 409,
        description: 'Il existe déjà un utilisateur avec cet email',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    public function makeUser(
        Request $request,
        UtilisateurRepository $uRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));

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
    #[Security(name: 'BearerAuth')]
    #[OA\RequestBody(
        required: true,
        description: 'Le body contient les informations de la pièce à créer',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'idUtilisateur', type: 'integer'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Piece créée avec succès',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 406,
        description: 'Manque de données requises',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    public function creePiece(
        Request $request,
        PieceRepository $pRepository,
        UtilisateurRepository $uRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));
        $piece = new Piece();
        if ($data->has('description') && !empty($data->getString('description'))) {
            $piece->setDescription($data->getString('description'));
        }
        if ($data->has('nom') && !empty($data->getString('nom'))) {
            $piece->setNom($data->getString('nom'));
        }
        if ($data->has('idUtilisateur') && !empty($data->getInt('idUtilisateur'))) {
            $piece->setUtilisateur($uRepository->find($data->getInt('idUtilisateur')));
        }

        $em->persist($piece);
        $em->flush();

        return $this->json(['message' => 'Piece crée'], 201);
    }

    #[Route('/piece/{piece<\d*>}', name: '_get_piece', methods: ['GET'])]
    #[Security(name: 'BearerAuth')]
    public function getPiece(Piece $piece): JsonResponse
    {
        return $this->json($this->serializer->normalize($piece, 'json', [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'nom',
                'prenom',
                'description',
                'capteur',
            ],
        ]));
    }
}
