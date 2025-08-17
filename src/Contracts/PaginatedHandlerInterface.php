<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Handler that executes the composed query using length-aware pagination.
 */
interface PaginatedHandlerInterface extends HandlerInterface
{
    /**
     * Execute the composed query and return a length-aware paginator.
     * Implementations MUST apply the queued criteria and use the configured Pagination value-object.
     *
     * @return LengthAwarePaginator The paginated result set.
     */
    public function get(): LengthAwarePaginator;
}
