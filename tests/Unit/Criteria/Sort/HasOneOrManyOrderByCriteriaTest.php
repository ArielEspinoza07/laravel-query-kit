<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\HasOneOrManyOrderByCriteria;

it('supports HasOneOrMany and rejects others', function () {
    $applier = new HasOneOrManyOrderByCriteria;

    expect($applier->supports(Mockery::mock(HasOneOrMany::class)))->toBeTrue()
        ->and($applier->supports(Mockery::mock(BelongsToMany::class)))->toBeFalse()
        ->and($applier->supports(Mockery::mock(Relation::class)))->toBeFalse();
});

it('apply uses addSelect(alias=>sub) then orderBy(alias)', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('addSelect')->once()->andReturnSelf();
    $qb->shouldReceive('orderBy')->once()->andReturnSelf();

    $sub = Mockery::mock(EloquentBuilder::class);
    $sub->shouldReceive('select')->andReturnSelf();
    $sub->shouldReceive('whereColumn')->andReturnSelf();
    $sub->shouldReceive('orderBy')->andReturnSelf();
    $sub->shouldReceive('limit')->andReturnSelf();

    $related = Mockery::mock(Model::class);
    $related->shouldReceive('qualifyColumn')->with('id')->andReturn('children.id');
    $related->shouldReceive('newQuery')->andReturn($sub);
    $related->shouldReceive('getTable')->andReturn('children');

    $rel = Mockery::mock(HasOneOrMany::class);
    $rel->shouldReceive('getRelated')->andReturn($related);
    $rel->shouldReceive('getQualifiedForeignKeyName')->andReturn('children.parent_id');
    $rel->shouldReceive('getQualifiedParentKeyName')->andReturn('parents.id');

    $applier = new HasOneOrManyOrderByCriteria;

    $res = $applier->apply($qb, Mockery::mock(Model::class), $rel, 'id', 'asc');

    expect($res)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
