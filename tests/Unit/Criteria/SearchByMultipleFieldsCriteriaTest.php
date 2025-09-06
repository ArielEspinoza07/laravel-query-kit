<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\SearchByMultipleFieldsCriteria;

it('fallback: applies where/orWhere over multiple columns when whereAny is unavailable', function () {
    $outer = Mockery::mock(Builder::class);

    $inner = Mockery::mock(Builder::class);

    $criteria = new SearchByMultipleFieldsCriteria(
        ['title', 'body', 'summary'],
        '%foo%',
        'like'
    );

    $outer->shouldReceive('where')
        ->once()
        ->with(Mockery::type('callable'))
        ->andReturnUsing(function (callable $closure) use ($inner, $outer) {
            $inner->shouldReceive('where')
                ->once()
                ->with('title', 'like', '%foo%')
                ->andReturn($inner);

            $inner->shouldReceive('orWhere')
                ->once()
                ->with('body', 'like', '%foo%')
                ->andReturn($inner);

            $inner->shouldReceive('orWhere')
                ->once()
                ->with('summary', 'like', '%foo%')
                ->andReturn($inner);

            $closure($inner);

            return $outer;
        });

    $result = $criteria->apply($outer);

    expect($result)->toBe($outer);
});

it('fallback: applies whereAny when is available', function () {
    $qb = Mockery::mock(QueryBuilder::class);

    $qb->shouldReceive('whereAny')
        ->once()
        ->with(['title', 'body', 'summary'], 'like', '%foo%')
        ->andReturnSelf();

    $criteria = new SearchByMultipleFieldsCriteria(
        ['title', 'body', 'summary'],
        '%foo%',
        'like',
    );

    $result = $criteria->apply($qb);

    expect($result)->toBe($qb);
});
