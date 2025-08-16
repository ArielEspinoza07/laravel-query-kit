<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Criteria\Sort\BelongsToManyOrderByCriteria;

it('supports BelongsToMany and rejects others', function () {
    $applier = new BelongsToManyOrderByCriteria;

    expect($applier->supports(Mockery::mock(BelongsToMany::class)))->toBeTrue()
        ->and($applier->supports(Mockery::mock(HasOne::class)))->toBeFalse()
        ->and($applier->supports(Mockery::mock(Relation::class)))->toBeFalse();
});

it('apply uses pivot+related joins and orderBy, returning builder', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $qb->shouldReceive('leftJoin')->twice()->andReturnSelf(); // pivot + related
    $qb->shouldReceive('orderBy')->once()->andReturnSelf();

    $related = Mockery::mock(Model::class);
    $related->shouldReceive('getTable')->andReturn('tags');
    $related->shouldReceive('qualifyColumn')->with('name')->andReturn('tags.name');

    $rel = Mockery::mock(BelongsToMany::class);
    $rel->shouldReceive('getRelated')->andReturn($related);
    $rel->shouldReceive('getTable')->andReturn('post_tag');
    $rel->shouldReceive('getQualifiedParentKeyName')->andReturn('posts.id');
    $rel->shouldReceive('getQualifiedRelatedKeyName')->andReturn('tags.id');
    $rel->shouldReceive('getQualifiedForeignPivotKeyName')->andReturn('post_tag.post_id');
    $rel->shouldReceive('getQualifiedRelatedPivotKeyName')->andReturn('post_tag.tag_id');

    $applier = new BelongsToManyOrderByCriteria;

    $res = $applier->apply($qb, Mockery::mock(Model::class), $rel, 'name', 'desc');

    expect($res)->toBe($qb);
});

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
