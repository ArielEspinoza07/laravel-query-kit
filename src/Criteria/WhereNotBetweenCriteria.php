<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class WhereNotBetweenCriteria implements CriteriaInterface
{
    public function __construct(
        private string $column,
        private iterable $values,
        private string $boolean = 'and',
    ) {}

    public function apply(Builder $builder): Builder
    {
        return $builder->whereNotBetween(
            column: $this->column,
            values: $this->values,
            boolean: $this->boolean,
        );
    }
}
