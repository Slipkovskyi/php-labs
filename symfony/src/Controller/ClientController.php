<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 */
#[Route('/client', name: 'client_routes')]
class ClientController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ClientRepository $clientRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ClientRepository $clientRepository
    ) {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_clients', methods: ['GET'])]
    public function getClients(): JsonResponse
    {
        $clients = $this->clientRepository->findAll();
        $data = array_map(fn(Client $client) => $client->jsonSerialize(), $clients);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'create_client', methods: ['POST'])]
    public function createClient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $client = new Client();
        $client->setName($data['name'] ?? '');
        $client->setPhone($data['phone'] ?? '');
        $client->setEmail($data['email'] ?? null);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return new JsonResponse($client->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_client', methods: ['GET'])]
    public function getClient(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($client->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'update_client', methods: ['PATCH'])]
    public function updateClient(Request $request, int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $client->setName($data['name']);
        }
        if (isset($data['phone'])) {
            $client->setPhone($data['phone']);
        }
        if (array_key_exists('email', $data)) {
            $client->setEmail($data['email']);
        }

        $this->entityManager->flush();

        return new JsonResponse($client->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_client', methods: ['DELETE'])]
    public function deleteClient(int $id): JsonResponse
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Client deleted successfully'], Response::HTTP_OK);
    }
}
