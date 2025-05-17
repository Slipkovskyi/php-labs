<?php

namespace App\Repositories;

use App\Models\Driver;
use App\PaginationService;

/**
 *
 */
class DriverRepository
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
        $query = Driver::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['phone'])) {
            $query->where('phone', 'like', '%' . $filters['phone'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $p = $this->pagination->paginate($query, $perPage, $page);

        return [
            'items'          => $p->items(),
            'totalPageCount' => $p->lastPage(),
            'totalItems'     => $p->total(),
        ];
    }
}
