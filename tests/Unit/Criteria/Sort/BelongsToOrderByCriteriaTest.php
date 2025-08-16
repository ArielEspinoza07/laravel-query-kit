<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\BelongsToOrderByCriteria;

it('supports BelongsTo and rejects others', function () {
    $applier = new BelongsToOrderByCriteria;

    expect($applier->supports(Mockery::mock(BelongsTo::class)))->toBeTrue();
    expect($applier->supports(Mockery::mock(HasMany::class)))->toBeFalse();
    expect($applier->supports(Mockery::mock(Relation::class)))->toBeFalse();
});

it('apply uses leftJoin/orderBy and returns builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('leftJoin')->andReturnSelf();
    $qb->shouldReceive('orderBy')->andReturnSelf();

    $related = Mockery::mock(Model::class);
    $related->shouldReceive('getTable')->andReturn('users');
    $related->shouldReceive('qualifyColumn')->with('email')->andReturn('users.email');

    $rel = Mockery::mock(BelongsTo::class);
    $rel->shouldReceive('getRelated')->andReturn($related);
    $rel->shouldReceive('getQualifiedForeignKeyName')->andReturn('posts.user_id');
    $rel->shouldReceive('getQualifiedOwnerKeyName')->andReturn('users.id');

    $applier = new BelongsToOrderByCriteria;

    $res = $applier->apply($qb, Mockery::mock(Model::class), $rel, 'email', 'asc');

    expect($res)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
