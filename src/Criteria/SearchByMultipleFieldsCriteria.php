<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class SearchByMultipleFieldsCriteria implements CriteriaInterface
{
    /**
     * @param  list<string>  $columns
     */
    public function __construct(
        private array $columns,
        private string $value,
        private string $operator = '=',
    ) {}

    public function apply(Builder $builder): Builder
    {
        $columns = array_values(array_unique(array_filter(
            array_map('trim', $this->columns),
            static fn ($c) => $c !== ''
        )));

        if ($columns === []) {
            return $builder;
        }

        /** @phpstan-ignore-next-line */
        if (method_exists($builder, 'whereAny')) {
            return $builder->whereAny(
                columns: $columns,
                operator: $this->operator,
                value: $this->value,
            );
        }

        return $builder->where(function (Builder $b) use ($columns) {
            foreach ($columns as $i => $col) {
                $b = $i === 0
                    ? $b->where($col, $this->operator, $this->value)
                    : $b->orWhere($col, $this->operator, $this->value);
            }
        });
    }
}
