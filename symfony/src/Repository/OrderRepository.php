<?php

namespace App\Repository;

use App\Entity\Order;
use App\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;

    /**
     * @param ManagerRegistry $registry
     * @param PaginationService $paginationService
     */
    public function __construct(ManagerRegistry $registry, PaginationService $paginationService)
    {
        parent::__construct($registry, Order::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'orders' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('order');

        if (!empty($data['clientId'])) {
            $queryBuilder->andWhere('order.client = :clientId')
                ->setParameter('clientId', $data['clientId']);
        }

        if (!empty($data['driverId'])) {
            $queryBuilder->andWhere('order.driver = :driverId')
                ->setParameter('driverId', $data['driverId']);
        }

        if (!empty($data['routeId'])) {
            $queryBuilder->andWhere('order.route = :routeId')
                ->setParameter('routeId', $data['routeId']);
        }

        if (!empty($data['status'])) {
            $queryBuilder->andWhere('order.status = :status')
                ->setParameter('status', $data['status']);
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }
}
