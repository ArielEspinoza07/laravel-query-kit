<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class WhereNotInCriteria implements CriteriaInterface
{
    public function __construct(
        private string $column,
        private array $values,
        private string $boolean = 'and',
    ) {}

    public function apply(Builder $builder): Builder
    {
        return $builder->whereNotIn(
            column: $this->column,
            values: $this->values,
            boolean: $this->boolean,
        );
    }
}
