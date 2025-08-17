<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\WhereNotInCriteria;

it('applies whereNotIn(column, values, boolean) and returns builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('whereNotIn')
        ->once()
        ->withArgs(function ($column, $values, $boolean) {
            return $column === 'id'
                && $values === [4, 5]
                && $boolean === 'and';
        })
        ->andReturnSelf();

    $criteria = new WhereNotInCriteria('id', [4, 5], 'and');

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});
