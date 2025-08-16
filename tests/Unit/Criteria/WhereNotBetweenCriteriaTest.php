<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\WhereNotBetweenCriteria;

it('applies whereNotBetween(column, [min, max], boolean) and returns builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('whereNotBetween')
        ->once()
        ->withArgs(function ($column, $values, $boolean) {
            return $column === 'price'
                && $values === [100, 200]
                && $boolean === 'and';
        })
        ->andReturnSelf();

    $criteria = new WhereNotBetweenCriteria('price', [100, 200], 'and');

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
