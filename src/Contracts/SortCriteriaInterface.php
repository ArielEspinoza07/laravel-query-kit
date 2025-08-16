<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

interface SortCriteriaInterface
{
    public function apply(Builder $builder, Model $model, Relation $relation, string $column, string $direction): Builder;

    public function supports(Relation $relation): bool;
}
