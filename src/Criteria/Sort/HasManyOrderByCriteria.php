<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria\Sort;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;

final class HasManyOrderByCriteria implements SortCriteriaInterface
{
    public function apply(
        Builder $builder,
        Model $model,
        Relation $relation,
        string $column,
        string $direction
    ): Builder {
        /** @var HasMany $relation */
        $related = $relation->getRelated();

        $sub = $related->newQuery()
            ->select($related->qualifyColumn($column))
            ->whereColumn($relation->getQualifiedForeignKeyName(), '=', $relation->getQualifiedParentKeyName())
            ->orderBy($column, $direction)
            ->limit(1);

        return $builder->orderBy($sub, $direction);
    }

    public function supports(Relation $relation): bool
    {
        return $relation instanceof HasMany;
    }
}
