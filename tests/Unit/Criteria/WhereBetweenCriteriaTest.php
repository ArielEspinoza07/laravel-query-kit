<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\WhereBetweenCriteria;

it('applies whereBetween(column, [min, max], boolean) and returns builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('whereBetween')
        ->once()
        ->withArgs(function ($column, $values, $boolean) {
            return $column === 'age'
                && $values === [18, 30]
                && $boolean === 'and';
        })
        ->andReturnSelf();

    $criteria = new WhereBetweenCriteria('age', [18, 30], 'and');

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
