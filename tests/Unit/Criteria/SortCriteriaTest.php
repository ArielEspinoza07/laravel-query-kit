<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;
use LaravelQueryKit\Criteria\Sort\OrderByCriteria;
use LaravelQueryKit\Criteria\SortCriteria;
use LaravelQueryKit\Exceptions\SortCriteriaException;

it('throws on invalid direction', function () {
    $model = new class extends Model
    {
        protected $table = 't';
    };
    new SortCriteria(model: $model, column: 'name', direction: 'up');
})->throws(SortCriteriaException::class);

it('orders directly with no relation path using default OrderByCriteria', function () {
    $qb = Mockery::mock(\Illuminate\Database\Query\Builder::class);
    $qb->shouldReceive('orderBy')
        ->once()
        ->with('name', 'asc')
        ->andReturnSelf();

    $model = new class extends Model
    {
        protected $table = 't';
    };

    $criterion = new SortCriteria(
        model: $model,
        column: 'name',
        direction: 'asc',
        sorts: [new OrderByCriteria], // defaultSort presente
    );

    $res = $criterion->apply($qb);

    expect($res)->toBe($qb);
});

it('delegates to the first applier supporting the relation', function () {
    $qb = Mockery::mock(QueryBuilder::class);

    $related = Mockery::mock(Model::class);
    $relation = Mockery::mock(Relation::class);
    $relation->shouldReceive('getRelated')->andReturn($related);

    $model = new class extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'parents';

        public ?\Illuminate\Database\Eloquent\Relations\Relation $rel = null;

        public function author()
        {
            return $this->rel;
        }
    };

    $model->rel = $relation;

    $chosen = Mockery::mock(SortCriteriaInterface::class);
    $chosen->shouldReceive('supports')
        ->once()
        ->with($relation)
        ->andReturnTrue();
    $chosen->shouldReceive('apply')
        ->once()
        ->with($qb, Mockery::type(Model::class), $relation, 'email', 'desc')
        ->andReturn($qb);

    $other = Mockery::mock(SortCriteriaInterface::class);
    $other->shouldReceive('supports')
        ->andReturnFalse();
    $other->shouldReceive('apply')
        ->never();

    $criterion = new SortCriteria(
        model: $model,
        column: 'author.email',
        direction: 'desc',
        sorts: [$other, $chosen, $other],
    );

    $res = $criterion->apply($qb);
    expect($res)->toBe($qb);
});

it('fails when relation method is missing', function () {
    $qb = Mockery::mock(QueryBuilder::class);
    $model = new class extends Model
    {
        protected $table = 'parents';
    };
    $criterion = new SortCriteria(
        model: $model,
        column: 'author.email',
        direction: 'asc',
        sorts: [],
    );

    $criterion->apply($qb);
})->throws(SortCriteriaException::class, 'Relation author not found');

it('fails when the method is not a Relation instance', function () {
    $qb = Mockery::mock(QueryBuilder::class);

    $model = new class extends Model
    {
        protected $table = 'parents';

        public function author()
        {
            return new stdClass;
        } // no Relation
    };

    $criterion = new SortCriteria(
        model: $model,
        column: 'author.email',
        direction: 'asc',
        sorts: [],
    );
    $criterion->apply($qb);
})->throws(SortCriteriaException::class, 'not a valid Eloquent relation');

it('fails when no applier supports the resolved relation', function () {
    $qb = Mockery::mock(QueryBuilder::class);

    $relation = Mockery::mock(Relation::class);
    $relation->shouldReceive('getRelated')->andReturn(Mockery::mock(Model::class));

    $model = new class extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'parents';

        public ?\Illuminate\Database\Eloquent\Relations\Relation $rel = null;

        public function author()
        {
            return $this->rel;
        }
    };

    $model->rel = $relation;

    $non = Mockery::mock(SortCriteriaInterface::class);
    $non->shouldReceive('supports')->andReturnFalse();

    $criterion = new SortCriteria(model: $model, column: 'author.email', direction: 'asc', sorts: [$non]);

    $criterion->apply($qb);
})->throws(SortCriteriaException::class, 'Unsupported relation type');

afterEach(function () {
    if (class_exists(Mockery::class)) {
        Mockery::close();
    }
});
