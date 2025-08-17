<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilderContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Exceptions\QueryBuilderException;
use LaravelQueryKit\QueryBuilder;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyJsonResource as QB_DummyJsonResource;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyResourceCollection as QB_DummyResourceCollection;

it('for(model) boots using model->newQuery() and exposes the same builder', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $qb = QueryBuilder::for($model);

    expect($qb->builder())->toBe($builder);
});

it('withPagination returns a new immutable instance', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $qb1 = QueryBuilder::for($model);
    $qb2 = $qb1->withPagination(page: 2, perPage: 10);

    expect($qb2)->not->toBe($qb1);
    // builder sigue accesible
    expect($qb2->builder())->toBe($builder);
});

it('withCriteria + addCriteria are applied in order when materializing toCollection()', function () {
    // Tres builders para simular la transformación A -> B -> C
    $qbA = Mockery::mock(QueryBuilderContract::class);
    $qbB = Mockery::mock(QueryBuilderContract::class);
    $qbC = Mockery::mock(QueryBuilderContract::class);

    // Modelo que devuelve el builder inicial A
    $model = new class extends Model
    {
        protected $table = 't';

        public $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }
    };
    $model->qb = $qbA;

    // Criterios: c1(A) => B, c2(B) => C
    $c1 = Mockery::mock(CriteriaInterface::class);
    $c1->shouldReceive('apply')->once()->with($qbA)->andReturn($qbB);

    $c2 = Mockery::mock(CriteriaInterface::class);
    $c2->shouldReceive('apply')->once()->with($qbB)->andReturn($qbC);

    // La terminal get() ocurre sobre C
    $rows = new Collection([1, 2, 3]);
    $qbC->shouldReceive('get')->once()->andReturn($rows);

    $out = QueryBuilder::for($model)
        ->withCriteria($c1)
        ->addCriteria($c2)
        ->toCollection();

    expect($out)->toBe($rows);
});

it('toModel() returns the first model', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $found = Mockery::mock(Model::class);
    $builder->shouldReceive('first')->once()->andReturn($found);

    $out = QueryBuilder::for($model)->toModel();

    expect($out)->toBe($found);
});

it('toCollection() returns a Collection', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $rows = collect([['id' => 1]]);
    $builder->shouldReceive('get')->once()->andReturn($rows);

    $out = QueryBuilder::for($model)->toCollection();

    expect($out)->toBeInstanceOf(Collection::class)->and($out)->toBe($rows);
});

it('toPaginated() calls paginate(perPage, page) and returns paginator', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $items = collect([['id' => 1], ['id' => 2]]);
    $paginator = new LengthAwarePaginator($items, $items->count(), 10, 2);

    $builder->shouldReceive('paginate')
        ->once()
        ->withArgs(function ($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null, $total = null) use ($paginator) {
            return $perPage === $paginator->perPage()
                && $page === $paginator->currentPage();
        })
        ->andReturn($paginator);

    $out = QueryBuilder::for($model)
        ->withPagination(page: 2, perPage: 10)
        ->toPaginated();

    expect($out)->toBe($paginator);
});

it('toPaginated() throws when pagination is not configured', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    QueryBuilder::for($model)->toPaginated();
})->throws(QueryBuilderException::class, 'Pagination fields are not initialized');

it('toResourceCollection() wraps a collection result into the given ResourceCollection', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $rows = collect([['id' => 1], ['id' => 2]]);
    $builder->shouldReceive('get')->once()->andReturn($rows);

    $out = QueryBuilder::for($model)->toResourceCollection(QB_DummyResourceCollection::class);

    expect($out)->toBeInstanceOf(QB_DummyResourceCollection::class);
});

it('toResourceCollection() wraps a paginator result when pagination is configured', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $items = collect([['id' => 1], ['id' => 2], ['id' => 3]]);
    $paginator = new LengthAwarePaginator($items, $items->count(), 5, 1);

    $builder->shouldReceive('paginate')
        ->once()
        ->andReturn($paginator);

    $out = QueryBuilder::for($model)
        ->withPagination(page: 1, perPage: 5)
        ->toResourceCollection(QB_DummyResourceCollection::class);

    expect($out)->toBeInstanceOf(QB_DummyResourceCollection::class);
});

it('toJsonResource() wraps the first model into the given JsonResource', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $found = Mockery::mock(Model::class);
    $builder->shouldReceive('first')->once()->andReturn($found);

    $out = QueryBuilder::for($model)->toJsonResource(QB_DummyJsonResource::class);

    expect($out)->toBeInstanceOf(QB_DummyJsonResource::class);
});
