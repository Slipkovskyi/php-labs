<?php

namespace app;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 *
 */
class PaginationService
{
    /**
     * @param Builder $query
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function paginate(Builder $query, int $perPage, int $page): LengthAwarePaginator
    {
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
