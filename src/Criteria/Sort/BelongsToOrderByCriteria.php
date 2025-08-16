<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria\Sort;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;

final readonly class BelongsToOrderByCriteria implements SortCriteriaInterface
{
    public function apply(
        Builder $builder,
        Model $model,
        Relation $relation,
        string $column,
        string $direction
    ): Builder {
        /** @var BelongsTo $relation */
        $related = $relation->getRelated();

        $relatedTable = $related->getTable();
        $parentKey = $relation->getQualifiedForeignKeyName();   // eg: posts.user_id
        $ownerKey = $relation->getQualifiedOwnerKeyName();     // eg: users.id

        $alias = $relatedTable; // optional alias

        $builder->leftJoin($relatedTable.' as '.$alias, $parentKey, '=', $ownerKey)
            ->orderBy($related->qualifyColumn($column), $direction);

        return $builder;
    }

    public function supports(Relation $relation): bool
    {
        return $relation instanceof BelongsTo;
    }
}
