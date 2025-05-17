<?php

namespace App\Repositories;

use App\Models\Order;
use App\PaginationService;

/**
 *
 */
class OrderRepository
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
        $query = Order::query();

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (!empty($filters['route_id'])) {
            $query->where('route_id', $filters['route_id']);
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
