<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ClientRepository;
use App\Repository\DriverRepository;
use App\Repository\RouteRepository;
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
#[Route('/order', name: 'order_routes')]
class OrderController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;
    /**
     * @var DriverRepository
     */
    private DriverRepository $driverRepository;
    /**
     * @var RouteRepository
     */
    private RouteRepository $routeRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param OrderRepository $orderRepository
     * @param ClientRepository $clientRepository
     * @param DriverRepository $driverRepository
     * @param RouteRepository $routeRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        OrderRepository $orderRepository,
        ClientRepository $clientRepository,
        DriverRepository $driverRepository,
        RouteRepository $routeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->clientRepository = $clientRepository;
        $this->driverRepository = $driverRepository;
        $this->routeRepository = $routeRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_CLIENT")]
    #[Route('/', name: 'get_orders', methods: ['GET'])]
    public function getOrders(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;
        $data = $this->orderRepository->getAllByFilter($requestData, $itemsPerPage, $page);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_MANAGER")]
    #[Route('/', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $client = $this->clientRepository->find($data['clientId'] ?? null);
        $driver = $this->driverRepository->find($data['driverId'] ?? null);
        $route = $this->routeRepository->find($data['routeId'] ?? null);

        if (!$client || !$driver || !$route) {
            return new JsonResponse(['message' => 'Invalid client, driver or route.'], Response::HTTP_BAD_REQUEST);
        }

        $order = new Order();
        $order->setClient($client);
        $order->setDriver($driver);
        $order->setRoute($route);
        $order->setPrice($data['price'] ?? '0.00');
        $order->setStatus($data['status'] ?? 'new');
        $order->setCreatedAt(new \DateTime());
        $order->setCompletedAt(null);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return new JsonResponse($order->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_CLIENT")]
    #[Route('/{id}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($order->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_MANAGER")]
    #[Route('/{id}', name: 'update_order', methods: ['PATCH'])]
    public function updateOrder(Request $request, int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['clientId'])) {
            $client = $this->clientRepository->find($data['clientId']);
            if ($client) {
                $order->setClient($client);
            }
        }

        if (isset($data['driverId'])) {
            $driver = $this->driverRepository->find($data['driverId']);
            if ($driver) {
                $order->setDriver($driver);
            }
        }

        if (isset($data['routeId'])) {
            $route = $this->routeRepository->find($data['routeId']);
            if ($route) {
                $order->setRoute($route);
            }
        }

        if (isset($data['price'])) {
            $order->setPrice($data['price']);
        }

        if (isset($data['status'])) {
            $order->setStatus($data['status']);
        }

        if (array_key_exists('completedAt', $data)) {
            $order->setCompletedAt($data['completedAt'] ? new \DateTime($data['completedAt']) : null);
        }

        $this->entityManager->flush();

        return new JsonResponse($order->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_order', methods: ['DELETE'])]
    public function deleteOrder(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Order deleted successfully'], Response::HTTP_OK);
    }
}
