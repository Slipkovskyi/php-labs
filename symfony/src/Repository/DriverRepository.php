<?php

namespace App\Repository;

use App\Entity\Driver;
use App\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Driver>
 */
class DriverRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Driver::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'drivers' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('driver');

        if (!empty($data['name'])) {
            $queryBuilder->andWhere('driver.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (!empty($data['phone'])) {
            $queryBuilder->andWhere('driver.phone LIKE :phone')
                ->setParameter('phone', '%' . $data['phone'] . '%');
        }

        if (!empty($data['status'])) {
            $queryBuilder->andWhere('driver.status = :status')
                ->setParameter('status', $data['status']);
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }
}
