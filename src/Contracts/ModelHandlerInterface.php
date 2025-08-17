<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Handler that executes the composed query and returns the first model (or null).
 */
interface ModelHandlerInterface extends HandlerInterface
{
    /**
     * Execute the composed query and return the first matching model.
     * Implementations MUST apply the queued criteria before executing the query.
     *
     * @return Model|null The first matching model, or null when no rows are found.
     */
    public function get(): ?Model;
}
