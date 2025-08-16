<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Eloquent\Builder;
use LaravelQueryKit\Criteria\IncludeRelationsCriteria;

it('calls with(relations) on Eloquent builder and returns builder', function () {
    $include = ['author', 'comments.replies'];

    $qb = Mockery::mock(Builder::class);
    $qb->shouldReceive('with')
        ->once()
        ->withArgs(function ($relations) use ($include) {
            return count($relations) === count($include)
                && $relations === $include;
        })
        ->andReturnSelf();

    $criteria = new IncludeRelationsCriteria($include);

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
