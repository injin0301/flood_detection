<?php

namespace App\Controller;

use App\Entity\Piece;
use App\Repository\PieceRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
#[OA\Tag(name: 'Pieces')]
final class ApiController extends AbstractController
{
    private Serializer $serializer;

    public function __construct()
    {
        $normalizer = [new ObjectNormalizer()];
        $encoding = [new JsonEncoder(), new XmlEncoder(), new CsvEncoder(), new YamlEncoder()];

        $this->serializer = new Serializer($normalizer, $encoding);
    }

    #[Route('/toutes/pieces', name: '_all_piece', methods: ['GET'])]
    #[OA\RequestBody(
        required: false,
        description: 'Le body peut contenir le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'csrf_token', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste de toutes les Pièces',
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

    #[Route('/piece/{piece<\d*>}/put', name: '_update_piece_put', methods: ['PUT'])]
    #[OA\RequestBody(
        required: true,
        description: 'Les informations de la pièce à mettre à jour pour PUT',
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
        description: 'Pièce modifiée avec succès',
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
    public function updatePiecePut(
        Piece $piece,
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $uRepository
    ): Response {
        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));

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

        return $this->json(['message' => 'Piece modifié'], 200);
    }


    #[Route('/piece/{piece<\d*>}/patch', name: '_update_piece_path', methods: ['PATCH'])]
    #[OA\RequestBody(
        required: false,
        description: 'Les informations de la pièce à mettre à jour pour PATCH.
            Ce n\'est pas obligatoire de tout mettre',
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
        description: 'Pièce modifiée avec succès',
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
    public function updatePiecePatch(
        Piece $piece,
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $uRepository
    ): JsonResponse {
        $data = new ParameterBag($this->serializer->decode($request->getContent(), 'json'));

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

        return $this->json(['message' => 'Piece modifié'], 200);
    }

    #[Route('/piece/{piece<\d*>}/suppression', name: '_delete_piece', methods: ['DELETE'])]
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
        description: 'Pièce supprimée avec succès',
    )]
    #[OA\Response(
        response: 404,
        description: 'Pièce non trouvée',
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

    #[Route('/piece/ajout', name: '_add_piece', methods: ['POST'])]
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
        description: 'Pièce créée avec succès',
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

        return $this->json(['message' => 'Pièce créée'], 201);
    }

    #[Route('/piece/{piece<\d*>}', name: '_get_piece', methods: ['GET'])]
    #[OA\RequestBody(
        required: false,
        description: 'Le body peut contenir le CSRF Token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'csrf_token', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Détails de la pièce',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'prenom', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(
                    property: 'capteur',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'humidite', type: 'float'),
                        new OA\Property(property: 'temperature', type: 'float'),
                        new OA\Property(property: 'niveau_eau', type: 'float'),
                        new OA\Property(property: 'inondation', type: 'bool'),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Pièce non trouvée',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'error', type: 'string'),
            ]
        )
    )]
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
