<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use LaravelQueryKit\Service\QueryService;

/**
 * Base contract for all result handlers (model/collection/pagination/resources).
 * A handler knows how to materialize the composed query from a QueryService.
 */
interface HandlerInterface
{
    /**
     * Factory that creates a concrete handler bound to the provided QueryService.
     * The concrete handler decides how to materialize results (model, collection, paginator, resources, etc.).
     *
     * @param  QueryService  $service  The prepared service whose query will be executed.
     * @return self A handler instance ready to fetch results.
     */
    public static function create(QueryService $service): self;
}
