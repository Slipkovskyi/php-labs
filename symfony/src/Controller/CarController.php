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
     * @return JsonResponse
     */
    #[Route('/', name: 'get_cars', methods: ['GET'])]
    public function getCars(): JsonResponse
    {
        $cars = $this->carRepository->findAll();
        $data = array_map(fn(Car $car) => $car->jsonSerialize(), $cars);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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
