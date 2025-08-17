<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\WhereFieldCriteria;

it('applies where(column, operator, value, boolean) and returns builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('where')
        ->once()
        ->withArgs(function ($column, $operator, $value, $boolean) {
            return $column === 'status'
                && $operator === '='
                && $value === 'active'
                && $boolean === 'and';
        })
        ->andReturnSelf();

    $criteria = new WhereFieldCriteria('status', '=', 'active', 'and');

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});
