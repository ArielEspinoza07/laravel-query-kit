<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class SelectColumnsCriteria implements CriteriaInterface
{
    /**
     * @param  list<string>  $columns
     */
    public function __construct(private array $columns = []) {}

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $builder): Builder
    {
        $columns = array_values(array_unique(array_filter(
            array_map('trim', $this->columns),
            static fn ($c) => $c !== ''
        )));

        if ($columns === []) {
            return $builder;
        }

        return $builder->select(columns: $columns);
    }
}
