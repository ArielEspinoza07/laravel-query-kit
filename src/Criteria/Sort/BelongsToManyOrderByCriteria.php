<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria\Sort;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;

final readonly class BelongsToManyOrderByCriteria implements SortCriteriaInterface
{
    public function apply(
        Builder $builder,
        Model $model,
        Relation $relation,
        string $column,
        string $direction
    ): Builder {
        /** @var BelongsToMany $relation */
        $related = $relation->getRelated();
        $pivot = $relation->getTable();
        $parentKey = $relation->getQualifiedParentKeyName();           // eg: posts.id
        $relatedKey = $relation->getQualifiedRelatedKeyName();          // eg: users.id
        $fkOnPivot = $relation->getQualifiedForeignPivotKeyName();     // eg: post_user.post_id
        $relatedPivot = $relation->getQualifiedRelatedPivotKeyName();     // eg: post_user.user_id

        $builder->leftJoin($pivot, $parentKey, '=', $fkOnPivot)
            ->leftJoin($related->getTable(), $relatedPivot, '=', $relatedKey)
            ->orderBy($related->qualifyColumn($column), $direction);

        return $builder;
    }

    public function supports(Relation $relation): bool
    {
        return $relation instanceof BelongsToMany;
    }
}
