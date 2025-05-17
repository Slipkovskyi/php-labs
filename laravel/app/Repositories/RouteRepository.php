<?php

namespace App\Repositories;

use App\Models\Route;
use App\PaginationService;

/**
 *
 */
class RouteRepository
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
        $query = Route::query();

        if (!empty($filters['start_point'])) {
            $query->where('start_point', 'like', '%' . $filters['start_point'] . '%');
        }

        if (!empty($filters['end_point'])) {
            $query->where('end_point', 'like', '%' . $filters['end_point'] . '%');
        }

        $p = $this->pagination->paginate($query, $perPage, $page);

        return [
            'items'          => $p->items(),
            'totalPageCount' => $p->lastPage(),
            'totalItems'     => $p->total(),
        ];
    }
}
