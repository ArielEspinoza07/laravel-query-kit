<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\HasManyThroughOrderByCriteria;

it('supports HasManyThrough and rejects others', function () {
    $applier = new HasManyThroughOrderByCriteria;

    expect($applier->supports(Mockery::mock(HasManyThrough::class)))->toBeTrue()
        ->and($applier->supports(Mockery::mock(HasOne::class)))->toBeFalse()
        ->and($applier->supports(Mockery::mock(Relation::class)))->toBeFalse();
});

it('apply builds existence subquery and orders by alias', function () {
    // Parent builder must be Eloquent\Builder
    $parent = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
    $parent->shouldReceive('addSelect')->once()->andReturnSelf();
    $parent->shouldReceive('orderBy')->once()->andReturnSelf();

    // Subquery builder returned by getRelationExistenceQuery(...)
    $sub = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
    $sub->shouldReceive('select')->andReturnSelf();
    $sub->shouldReceive('orderBy')->andReturnSelf();
    $sub->shouldReceive('limit')->andReturnSelf();

    // Related model: column qualifier, newQuery and (important!) getTable()
    $related = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
    $related->shouldReceive('qualifyColumn')
        ->with('name')
        ->andReturn('targets.name');
    $related->shouldReceive('newQuery')
        ->andReturn(Mockery::mock(\Illuminate\Database\Eloquent\Builder::class));
    $related->shouldReceive('getTable')      // <- agrega esta expectativa
    ->atLeast()->once()
        ->andReturn('targets');

    // HasManyThrough
    $rel = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class);
    $rel->shouldReceive('getRelated')->andReturn($related);
    $rel->shouldReceive('getRelationExistenceQuery')
        ->once()
        ->with(Mockery::type(\Illuminate\Database\Eloquent\Builder::class), $parent)
        ->andReturn($sub);

    $applier = new \LaravelQueryKit\Criteria\Sort\HasManyThroughOrderByCriteria;

    $res = $applier->apply(
        builder:   $parent,
        model:     Mockery::mock(\Illuminate\Database\Eloquent\Model::class),
        relation:  $rel,
        column:    'name',
        direction: 'desc'
    );

    expect($res)->toBe($parent);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
