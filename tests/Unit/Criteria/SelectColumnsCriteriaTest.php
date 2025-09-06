<?php

declare(strict_types=1);

use Illuminate\Database\Query\Builder;
use LaravelQueryKit\Criteria\SelectColumnsCriteria;

it('calls select(columns) on Eloquent builder and returns builder', function () {
    $columns = ['id', 'title'];

    $qb = Mockery::mock(Builder::class);

    $qb->shouldReceive('select')
        ->once()
        ->with($columns)
        ->andReturnSelf();

    $criteria = new SelectColumnsCriteria($columns);

    $res = $criteria->apply($qb);

    expect($res)->toBe($qb);
});
