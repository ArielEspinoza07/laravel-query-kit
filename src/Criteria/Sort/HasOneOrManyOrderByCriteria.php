<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria\Sort;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;

final class HasOneOrManyOrderByCriteria implements SortCriteriaInterface
{
    public function apply(
        Builder $builder,
        Model $model,
        Relation $relation,
        string $column,
        string $direction
    ): Builder {
        /** @var HasOneOrMany $relation */
        $related = $relation->getRelated();
        $qualified = $related->qualifyColumn($column);

        // Subquery: Takes the first child ordered by $column ($address)
        // and uses it to sort the parent without duplicating it.
        $sub = $related->newQuery()
            ->select($qualified)
            ->whereColumn(
                $relation->getQualifiedForeignKeyName(),   // child.fk
                '=',
                $relation->getQualifiedParentKeyName()     // father.pk
            )
            ->orderBy($qualified, $direction)
            ->limit(1);

        // For broad compatibility, we use addSelect-subquery + orderBy(alias)
        $alias = '__lqk_sort_'.$related->getTable().'_'.$column;

        return $builder
            ->addSelect([$alias => $sub])
            ->orderBy($alias, $direction);
    }

    public function supports(Relation $relation): bool
    {
        return $relation instanceof HasOneOrMany;
    }
}
