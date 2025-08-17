<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use LaravelQueryKit\Criteria\SearchByMultipleFieldsCriteria;

it('fallback: applies where/orWhere over multiple columns when whereAny is unavailable', function () {
    $qb = Mockery::mock(Builder::class);

    $qb->shouldReceive('where')
        ->once()
        ->with('title', 'like', '%foo%')
        ->andReturnSelf();

    $qb->shouldReceive('orWhere')
        ->once()
        ->with('body', 'like', '%foo%')
        ->andReturnSelf();
    $qb->shouldReceive('orWhere')
        ->once()
        ->with('summary', 'like', '%foo%')
        ->andReturnSelf();

    $criteria = new SearchByMultipleFieldsCriteria(
        ['title', 'body', 'summary'],
        '%foo%',
        'like'
    );

    $res = $criteria->apply($qb);

    expect($res)->toBe($qb);
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
