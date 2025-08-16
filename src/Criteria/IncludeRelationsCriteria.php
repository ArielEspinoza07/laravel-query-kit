<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class IncludeRelationsCriteria implements CriteriaInterface
{
    /**
     * @param  array<string>  $relations
     */
    public function __construct(private array $relations) {}

    /**
     * @param  EloquentBuilder  $builder
     */
    public function apply(Builder $builder): Builder
    {
        return $builder->with(relations: $this->relations);
    }
}
