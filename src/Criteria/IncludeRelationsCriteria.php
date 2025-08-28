<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Exceptions\CriteriaException;

final readonly class IncludeRelationsCriteria implements CriteriaInterface
{
    /**
     * @param  array<string>  $relations
     */
    public function __construct(private array $relations) {}

    /**
     * @throws CriteriaException
     */
    public function apply(Builder $builder): Builder
    {
        if (! $builder instanceof EloquentBuilder) {
            throw new CriteriaException('IncludeRelationsCriteria requires an Eloquent builder.');
        }

        return $builder->with(relations: $this->relations);
    }
}
