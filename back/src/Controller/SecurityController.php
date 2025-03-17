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
use Symfony\Component\HttpFoundation\Response;
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
#[OA\Tag(name: 'Login Check')]
final class SecurityController extends AbstractController
{
    private Serializer $serializer;

    public function __construct()
    {
        $normalizer = [new ObjectNormalizer()];
        $encoding = [new JsonEncoder(), new XmlEncoder(), new CsvEncoder(), new YamlEncoder()];

        $this->serializer = new Serializer($normalizer, $encoding);
    }

    /**
     * Authentifie un utilisateur et génère un token JWT.
     *
     * @param Request $request La requête HTTP.
     * @param UtilisateurRepository $uRepository Le dépôt des utilisateurs.
     * @param HexTextProtectRepository $htRepository Le dépôt des protections de texte hexadécimal.
     * @param UserPasswordHasherInterface $passwordHasher Le service de hachage de mot de passe.
     * @param JWTTokenManagerInterface $jwtManager Le gestionnaire de tokens JWT.
     * @param HexTextService $hexTextService Le service de texte hexadécimal.
     *
     * @return JsonResponse La réponse JSON contenant le token JWT.
     */
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
        description: 'Manque de l\'email ou du mot de passe',
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
            return $this->json(['err' => 'Manque de l\'email ou du mot de passe'], 406);
        }
        $verif = $uRepository->findOneBy(['email' => $data->get('email')]);

        $passwordValid = $passwordHasher->isPasswordValid($verif, $data->getString('password'));

        if (!$passwordValid) {
            return $this->json(['err' => 'Utilisateur non trouvé'], 404);
        }

        $hexTextService->setUtilisateur($verif);
        $hexText = $hexTextService->generateHexText();
        $hexTextEntity = $hexTextService->saveHexText($hexText);

        $token = $jwtManager->createFromPayload(
            $verif,
            ['passphrase' => $hexTextEntity->getPassFrase()]
        );

        return $this->json(['token' => $token]);
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * @param Request $request La requête HTTP.
     * @param UtilisateurRepository $uRepository Le dépôt des utilisateurs.
     * @param EntityManagerInterface $em Le gestionnaire d'entités.
     * @param UserPasswordHasherInterface $passwordHasher Le service de hachage de mot de passe.
     *
     * @return JsonResponse La réponse JSON indiquant le succès ou l'échec de la création de l'utilisateur.
     */
    #[Route('/enregistrer/utilisateur', name: '_register_user', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        description: 'Pour créer un utilisateur',
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
        description: 'Manque de l\'email ou du mot de passe',
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
            return $this->json(['err' => 'Manque de l\'email ou du mot de passe'], 406);
        }

        $userEmail = $uRepository->findOneBy(['email' => $data->getString('email')]);
        if (!empty($userEmail)) {
            return $this->json(['err' => 'Il existe déjà un utilisateur avec cet email'], 409);
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

        return $this->json([
            'message' => 'Utilisateur créé'
        ], 201);
    }
}
