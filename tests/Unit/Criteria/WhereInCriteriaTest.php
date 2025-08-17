<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\WhereInCriteria;

it('applies whereIn(column, values, boolean) and returns builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('whereIn')
        ->once()
        ->withArgs(function ($column, $values, $boolean) {
            return $column === 'id'
                && $values === [1, 2, 3]
                && $boolean === 'and';
        })
        ->andReturnSelf();

    $criteria = new WhereInCriteria('id', [1, 2, 3], 'and');

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});
