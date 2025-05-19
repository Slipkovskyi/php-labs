<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 *
 */
#[Route('/car', name: 'car_routes')]
class CarController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var CarRepository
     */
    private CarRepository $carRepository;
    /**
     * @var DriverRepository
     */
    private DriverRepository $driverRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CarRepository $carRepository
     * @param DriverRepository $driverRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CarRepository $carRepository,
        DriverRepository $driverRepository
    ) {
        $this->entityManager = $entityManager;
        $this->carRepository = $carRepository;
        $this->driverRepository = $driverRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_CLIENT")]
    #[Route('/', name: 'get_cars', methods: ['GET'])]
    public function getCars(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $data = $this->carRepository->getAllByFilter($requestData, $itemsPerPage, $page);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_MANAGER")]
    #[Route('/', name: 'create_car', methods: ['POST'])]
    public function createCar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $driver = $this->driverRepository->find($data['driverId'] ?? null);
        if (!$driver) {
            return new JsonResponse(['message' => 'Invalid driver ID'], Response::HTTP_BAD_REQUEST);
        }

        $car = new Car();
        $car->setModel($data['model'] ?? '');
        $car->setLicensePlate($data['licensePlate'] ?? '');
        $car->setDriver($driver);

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return new JsonResponse($car->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_CLIENT")]
    #[Route('/{id}', name: 'get_car', methods: ['GET'])]
    public function getCar(int $id): JsonResponse
    {
        $car = $this->carRepository->find($id);
        if (!$car) {
            return new JsonResponse(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($car->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_MANAGER")]
    #[Route('/{id}', name: 'update_car', methods: ['PATCH'])]
    public function updateCar(Request $request, int $id): JsonResponse
    {
        $car = $this->carRepository->find($id);
        if (!$car) {
            return new JsonResponse(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['model'])) {
            $car->setModel($data['model']);
        }
        if (isset($data['licensePlate'])) {
            $car->setLicensePlate($data['licensePlate']);
        }
        if (isset($data['driverId'])) {
            $driver = $this->driverRepository->find($data['driverId']);
            if ($driver) {
                $car->setDriver($driver);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse($car->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_car', methods: ['DELETE'])]
    public function deleteCar(int $id): JsonResponse
    {
        $car = $this->carRepository->find($id);
        if (!$car) {
            return new JsonResponse(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($car);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Car deleted successfully'], Response::HTTP_OK);
    }
}
