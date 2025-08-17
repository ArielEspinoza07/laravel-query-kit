<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\HasManyOrderByCriteria;

it('supports HasMany and rejects others', function () {
    $applier = new HasManyOrderByCriteria;

    expect($applier->supports(Mockery::mock(HasMany::class)))->toBeTrue()
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
    $related->shouldReceive('qualifyColumn')->with('updated_at')->andReturn('comments.updated_at');
    $related->shouldReceive('newQuery')->andReturn($sub);

    $rel = Mockery::mock(HasMany::class);
    $rel->shouldReceive('getRelated')->andReturn($related);
    $rel->shouldReceive('getQualifiedForeignKeyName')->andReturn('comments.post_id');
    $rel->shouldReceive('getQualifiedParentKeyName')->andReturn('posts.id');

    $applier = new HasManyOrderByCriteria;

    $res = $applier->apply($qb, Mockery::mock(Model::class), $rel, 'updated_at', 'desc');

    expect($res)->toBe($qb);
});
