<?php

namespace App\Repository;

use App\Entity\Car;
use App\PaginationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;

    /**
     * @param ManagerRegistry $registry
     * @param PaginationService $paginationService
     */
    public function __construct(
        ManagerRegistry $registry,
        PaginationService $paginationService
    ) {
        parent::__construct($registry, Car::class);
        $this->paginationService = $paginationService;
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    #[ArrayShape([
        'cars' => "array",
        'totalPageCount' => "float",
        'totalItems' => "int"
    ])]
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('car');

        if (!empty($data['model'])) {
            $queryBuilder->andWhere('car.model LIKE :model')
                ->setParameter('model', '%' . $data['model'] . '%');
        }

        if (!empty($data['licensePlate'])) {
            $queryBuilder->andWhere('car.licensePlate LIKE :licensePlate')
                ->setParameter('licensePlate', '%' . $data['licensePlate'] . '%');
        }

        if (!empty($data['driverId'])) {
            $queryBuilder->andWhere('car.driver = :driverId')
                ->setParameter('driverId', $data['driverId']);
        }

        return $this->paginationService->paginate($queryBuilder, $itemsPerPage, $page);
    }
}
