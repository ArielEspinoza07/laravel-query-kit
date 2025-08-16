<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria\Sort;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;

final readonly class OrderByCriteria implements SortCriteriaInterface
{
    public function apply(
        Builder $builder,
        Model $model,
        Relation $relation,
        string $column,
        string $direction
    ): Builder {
        return $builder;
    }

    public function applyWithoutRelation(Builder $builder, string $column, string $direction): Builder
    {
        $builder->orderBy(
            column: $column,
            direction: $direction,
        );

        return $builder;
    }

    public function supports(Relation $relation): bool
    {
        return false;
    }
}
