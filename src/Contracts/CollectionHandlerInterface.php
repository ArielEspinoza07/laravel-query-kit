<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Support\Collection;

/**
 * Handler that executes the composed query and returns all results as a collection.
 */
interface CollectionHandlerInterface extends HandlerInterface
{
    /**
     * Execute the composed query and return all matching rows as a Laravel Collection.
     * Implementations MUST apply the queued criteria before executing the query.
     *
     * @return Collection<array-key, mixed> Result set as a collection (typically of models or arrays).
     */
    public function get(): Collection;
}
