<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Contract for sort appliers that know how to order a parent query by a related column.
 */
interface SortCriteriaInterface
{
    /**
     * Apply an ORDER BY for a related column resolved through the given relation.
     * Implementations may use joins or correlated subqueries depending on the relation type.
     *
     * @param  Builder  $builder  The parent query to sort.
     * @param  Model  $model  The model owning the relation being sorted by.
     * @param  Relation  $relation  The resolved Eloquent relation instance.
     * @param  string  $column  Target column on the related model to sort by.
     * @param  string  $direction  Sort direction: 'asc' or 'desc'.
     * @return Builder The builder with ordering applied.
     */
    public function apply(Builder $builder, Model $model, Relation $relation, string $column, string $direction): Builder;

    /**
     * Declare whether this sorter can handle the provided Eloquent relation type.
     *
     * @param  Relation  $relation  The relation to test (e.g., BelongsTo, HasMany, BelongsToMany, ...).
     * @return bool True when this sorter supports the relation; false otherwise.
     */
    public function supports(Relation $relation): bool;
}
