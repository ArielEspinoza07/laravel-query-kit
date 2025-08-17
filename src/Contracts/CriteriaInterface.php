<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Contracts\Database\Query\Builder;

/**
 * Small, composable unit that mutates a query builder without executing it.
 */
interface CriteriaInterface
{
    /**
     * Apply this criterion to the given query builder and return the (possibly mutated) builder.
     *
     * Implementations MUST NOT execute the query; they should only modify the builder so that
     * additional criteria can be chained safely.
     *
     * @param  Builder  $builder  The base query to alter.
     * @return Builder The same builder instance, or a compatible one, after applying the criterion.
     */
    public function apply(Builder $builder): Builder;
}
