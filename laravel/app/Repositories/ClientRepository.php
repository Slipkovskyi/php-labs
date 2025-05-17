<?php

namespace App\Repositories;

use App\Models\Client;
use App\PaginationService;

/**
 *
 */
class ClientRepository
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
        $query = Client::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['phone'])) {
            $query->where('phone', 'like', '%' . $filters['phone'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        $p = $this->pagination->paginate($query, $perPage, $page);

        return [
            'items'          => $p->items(),
            'totalPageCount' => $p->lastPage(),
            'totalItems'     => $p->total(),
        ];
    }
}
