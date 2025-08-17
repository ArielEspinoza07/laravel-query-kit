<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\OrderByCriteria;

it('supports() returns false for any relation (used only as default non-relation sorter)', function () {
    $rel = Mockery::mock(Relation::class);

    $applier = new OrderByCriteria;

    expect($applier->supports($rel))->toBeFalse();
});

it('apply(...) with relation context returns builder untouched', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $rel = Mockery::mock(Relation::class);

    $applier = new OrderByCriteria;

    $res = $applier->apply($qb, Mockery::mock(Model::class), $rel, 'name', 'asc');

    expect($res)->toBe($qb);
});

it('applyWithoutRelation orders by column/direction and returns builder', function () {
    $qb = Mockery::mock(\Illuminate\Database\Query\Builder::class);

    $qb->shouldReceive('orderBy')
        ->once()
        ->with('name', 'asc')
        ->andReturnSelf();

    $applier = new OrderByCriteria;

    $res = $applier->applyWithoutRelation(
        builder: $qb,
        column: 'name',
        direction: 'asc',
    );

    expect($res)->toBe($qb);
});
