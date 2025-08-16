<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class WhereFieldCriteria implements CriteriaInterface
{
    public function __construct(
        private string $column,
        private mixed $operator = null,
        private mixed $value = null,
        private string $boolean = 'and',
    ) {}

    public function apply(Builder $builder): Builder
    {
        return $builder->where(
            column: $this->column,
            operator: $this->operator,
            value: $this->value,
            boolean: $this->boolean,
        );
    }
}
