<?php

namespace App\Controller;

use App\Entity\Route;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as SymfonyRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *
 */
#[SymfonyRoute('/route', name: 'route_routes')]
class RouteController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var RouteRepository
     */
    private RouteRepository $routeRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RouteRepository $routeRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RouteRepository $routeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->routeRepository = $routeRepository;
    }

    /**
     * @return JsonResponse
     */
    #[SymfonyRoute('/', name: 'get_routes', methods: ['GET'])]
    public function getRoutes(): JsonResponse
    {
        $routes = $this->routeRepository->findAll();
        $data = array_map(fn(Route $route) => $route->jsonSerialize(), $routes);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[SymfonyRoute('/', name: 'create_route', methods: ['POST'])]
    public function createRoute(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $route = new Route();
        $route->setStartPoint($data['startPoint'] ?? '');
        $route->setEndPoint($data['endPoint'] ?? '');
        $route->setDistanceKm($data['distanceKm'] ?? '0.00');

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        return new JsonResponse($route->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[SymfonyRoute('/{id}', name: 'get_route', methods: ['GET'])]
    public function getRoute(int $id): JsonResponse
    {
        $route = $this->routeRepository->find($id);
        if (!$route) {
            return new JsonResponse(['message' => 'Route not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($route->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[SymfonyRoute('/{id}', name: 'update_route', methods: ['PATCH'])]
    public function updateRoute(Request $request, int $id): JsonResponse
    {
        $route = $this->routeRepository->find($id);
        if (!$route) {
            return new JsonResponse(['message' => 'Route not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['startPoint'])) {
            $route->setStartPoint($data['startPoint']);
        }
        if (isset($data['endPoint'])) {
            $route->setEndPoint($data['endPoint']);
        }
        if (isset($data['distanceKm'])) {
            $route->setDistanceKm($data['distanceKm']);
        }

        $this->entityManager->flush();

        return new JsonResponse($route->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[SymfonyRoute('/{id}', name: 'delete_route', methods: ['DELETE'])]
    public function deleteRoute(int $id): JsonResponse
    {
        $route = $this->routeRepository->find($id);
        if (!$route) {
            return new JsonResponse(['message' => 'Route not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($route);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Route deleted successfully'], Response::HTTP_OK);
    }
}
