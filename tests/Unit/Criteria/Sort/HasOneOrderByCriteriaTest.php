<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\HasOneOrderByCriteria;

it('supports HasOne and rejects others', function () {
    $applier = new HasOneOrderByCriteria;

    expect($applier->supports(Mockery::mock(HasOne::class)))->toBeTrue()
        ->and($applier->supports(Mockery::mock(BelongsTo::class)))->toBeFalse()
        ->and($applier->supports(Mockery::mock(Relation::class)))->toBeFalse();
});

it('apply builds subquery and orders by it', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('orderBy')->once()->andReturnSelf();

    $sub = Mockery::mock(EloquentBuilder::class);
    $sub->shouldReceive('select')->andReturnSelf();
    $sub->shouldReceive('whereColumn')->andReturnSelf();
    $sub->shouldReceive('orderBy')->andReturnSelf();
    $sub->shouldReceive('limit')->andReturnSelf();

    $related = Mockery::mock(Model::class);
    $related->shouldReceive('qualifyColumn')->with('created_at')->andReturn('profiles.created_at');
    $related->shouldReceive('newQuery')->andReturn($sub);

    $rel = Mockery::mock(HasOne::class);
    $rel->shouldReceive('getRelated')->andReturn($related);
    $rel->shouldReceive('getQualifiedForeignKeyName')->andReturn('profiles.user_id');
    $rel->shouldReceive('getQualifiedParentKeyName')->andReturn('users.id');

    $applier = new HasOneOrderByCriteria;

    $res = $applier->apply($qb, Mockery::mock(Model::class), $rel, 'created_at', 'asc');

    expect($res)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
