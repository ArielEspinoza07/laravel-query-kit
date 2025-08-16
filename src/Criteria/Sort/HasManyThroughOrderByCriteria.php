<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria\Sort;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;
use LaravelQueryKit\Exceptions\SortCriteriaException;

final class HasManyThroughOrderByCriteria implements SortCriteriaInterface
{
    public function apply(
        Builder $builder,
        Model $model,
        Relation $relation,
        string $column,
        string $direction
    ): Builder {
        if (! $builder instanceof EloquentBuilder) {
            throw new SortCriteriaException(
                'HasManyThroughOrderByCriteria requires an Eloquent builder.'
            );
        }

        /** @var HasManyThrough $relation */
        $related = $relation->getRelated();
        $qualified = $related->qualifyColumn($column);

        // We build a correlated subquery using the relation's own logic:
        // This creates the correct JOIN/WhereColumns between Parent -> Through -> Related.
        $sub = $relation
            ->getRelationExistenceQuery($related->newQuery(), $builder) // Eloquent\Builder mapped to parent
            ->select($qualified)
            ->orderBy($qualified, $direction)
            ->limit(1);

        // Instead of direct orderBy(subquery) (not always supported by all drivers),
        // we use an alias via addSelect(...) and order by that alias (more portable).
        $alias = '__lqk_sort_'.$related->getTable().'_'.$column;

        return $builder
            ->addSelect([$alias => $sub])
            ->orderBy($alias, $direction);
    }

    public function supports(Relation $relation): bool
    {
        return $relation instanceof HasManyThrough;
    }
}
