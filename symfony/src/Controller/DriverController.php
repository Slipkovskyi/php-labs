<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 */
#[Route('/driver', name: 'driver_routes')]
class DriverController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var DriverRepository
     */
    private DriverRepository $driverRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param DriverRepository $driverRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DriverRepository $driverRepository
    ) {
        $this->entityManager = $entityManager;
        $this->driverRepository = $driverRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'get_drivers', methods: ['GET'])]
    public function getDrivers(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $data = $this->driverRepository->getAllByFilter($requestData, $itemsPerPage, $page);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'create_driver', methods: ['POST'])]
    public function createDriver(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $driver = new Driver();
        $driver->setName($data['name'] ?? '');
        $driver->setPhone($data['phone'] ?? '');
        $driver->setStatus($data['status'] ?? 'active');

        $this->entityManager->persist($driver);
        $this->entityManager->flush();

        return new JsonResponse($driver->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_driver', methods: ['GET'])]
    public function getDriver(int $id): JsonResponse
    {
        $driver = $this->driverRepository->find($id);
        if (!$driver) {
            return new JsonResponse(['message' => 'Driver not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($driver->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'update_driver', methods: ['PATCH'])]
    public function updateDriver(Request $request, int $id): JsonResponse
    {
        $driver = $this->driverRepository->find($id);
        if (!$driver) {
            return new JsonResponse(['message' => 'Driver not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $driver->setName($data['name']);
        }
        if (isset($data['phone'])) {
            $driver->setPhone($data['phone']);
        }
        if (isset($data['status'])) {
            $driver->setStatus($data['status']);
        }

        $this->entityManager->flush();

        return new JsonResponse($driver->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_driver', methods: ['DELETE'])]
    public function deleteDriver(int $id): JsonResponse
    {
        $driver = $this->driverRepository->find($id);
        if (!$driver) {
            return new JsonResponse(['message' => 'Driver not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($driver);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Driver deleted successfully'], Response::HTTP_OK);
    }
}
