<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Contracts\Database\Query\Builder;

interface CriteriaInterface
{
    public function apply(Builder $builder): Builder;
}
