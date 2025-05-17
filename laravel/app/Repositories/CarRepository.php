<?php

namespace App\Repositories;

use App\Models\Car;
use App\PaginationService;

/**
 *
 */
class CarRepository
{
    /**
     * @var PaginationService
     */
    private PaginationService $pagination;

    /**
     * @param PaginationService $pagination
     */
    public function __construct(PaginationService $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @param array $filters
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getAllByFilter(array $filters, int $perPage, int $page): array
    {
        $query = Car::query();

        if (!empty($filters['model'])) {
            $query->where('model', 'like', '%' . $filters['model'] . '%');
        }

        if (!empty($filters['license_plate'])) {
            $query->where('license_plate', 'like', '%' . $filters['license_plate'] . '%');
        }

        if (!empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        $p = $this->pagination->paginate($query, $perPage, $page);

        return [
            'items'          => $p->items(),
            'totalPageCount' => $p->lastPage(),
            'totalItems'     => $p->total(),
        ];
    }
}
