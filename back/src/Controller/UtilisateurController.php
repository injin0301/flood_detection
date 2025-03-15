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
#[Security(name: 'BearerAuth')]
#[OA\Tag(name: 'Utilisateur')]
final class UtilisateurController extends AbstractController
{
    private Serializer $serializer;

    public function __construct()
    {
        $normalizer = [new ObjectNormalizer()];
        $encoding = [new JsonEncoder(), new XmlEncoder(), new CsvEncoder(), new YamlEncoder()];

        $this->serializer = new Serializer($normalizer, $encoding);
    }

    #[Route('/tous/utilisateurs', name: '_tous_utilisateur', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Liste de tous les Utilisateurs',
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

    #[Route('/utilisateur/{utilisateur<\d*>}', name: '_utilisateur_put', methods: ['PUT'])]
    #[OA\RequestBody(
        required: true,
        description: 'Les informations de l\'utilisateur à mettre à jour',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'prenom', type: 'string'),
                new OA\Property(property: 'tel', type: 'string'),
                new OA\Property(property: 'city', type: 'string'),
                new OA\Property(property: 'zipCode', type: 'integer'),
                new OA\Property(property: 'piece', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                ]),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Utilisateur modifié',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 406,
        description: 'Erreur de validation des données',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'err', type: 'string'),
            ]
        )
    )]
    public function updateUtilisateur(
        Utilisateur $utilisateur,
        Request $request,
        EntityManagerInterface $em,
        PieceRepository $pRepository,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {

        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));

        foreach (['email','roles','password','nom','prenom','tel','city','zipCode','piece'] as $value) {
            if (!$data->has($value)) {
                return $this->json(['err' => "Manque de {$value}"], 406);
            }
        }

        if (filter_var($data->get('email'), FILTER_VALIDATE_EMAIL)) {
            $utilisateur->setEmail($data->get('email'));
        } else {
            return $this->json(['err' => 'Invalid email format'], 406);
        }

        $roles = $data->get('roles');
        if (is_array($roles) && !empty($roles)) {
            $utilisateur->setRoles($roles);
        } else {
            return $this->json(['err' => 'Invalid roles format'], 406);
        }

        $has = $passwordHasher->hashPassword($utilisateur, $data->getString('password'));
        $utilisateur->setPassword($has);

        if (is_string($data->get('nom'))) {
            $utilisateur->setNom($data->get('nom'));
        } else {
            return $this->json(['err' => 'Invalid nom format'], 406);
        }

        if (is_string($data->get('prenom'))) {
            $utilisateur->setPrenom($data->get('prenom'));
        } else {
            return $this->json(['err' => 'Invalid prenom format'], 406);
        }

        if (preg_match('/^\+?[0-9\s\-\(\)]{10,20}$/', $data->get('tel'))) {
            $utilisateur->setTel($data->get('tel'));
        } else {
            return $this->json(['err' => 'Invalid tel format'], 406);
        }

        if (is_string($data->get('city'))) {
            $utilisateur->setCity($data->get('city'));
        } else {
            return $this->json(['err' => 'Invalid city format'], 406);
        }

        if (preg_match('/^\d{5}$/', $data->get('zipCode'))) {
            $utilisateur->setZipCode($data->get('zipCode'));
        } else {
            return $this->json(['err' => 'Invalid zipCode format'], 406);
        }

        $pieceData = $data->get('piece');
        if (is_array($pieceData) && isset($pieceData['id'])) {
            $piece = $pRepository->find($pieceData['id']);
            if (!$piece) {
                $piece = new Piece();
                $piece->setNom('');
                $piece->setDescription('');
                $em->persist($piece);
                $em->flush();
            }
            $utilisateur->addPiece($piece);
        } else {
            return $this->json(['err' => 'Invalid piece format'], 406);
        }

        return $this->json(['message' => 'Utilisateur modifié'], 201);
    }
}
