<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilderContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Exceptions\QueryBuilderException;
use LaravelQueryKit\QueryBuilder;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyJsonResource as QB_DummyJsonResource;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyResourceCollection as QB_DummyResourceCollection;

it('for(model) boots the builder using model->newQuery() and exposes the same builder', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $qb = QueryBuilder::for($model);

    expect($qb->builder())->toBe($builder);
});

it('withCriteria + addCriteria are applied in order when materializing toCollection()', function () {
    // three builders to simulate transformation
    $qbA = Mockery::mock(QueryBuilderContract::class);
    $qbB = Mockery::mock(QueryBuilderContract::class);
    $qbC = Mockery::mock(QueryBuilderContract::class);

    // initial model->newQuery() returns qbA
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

    // criteria chain: c1(A)->B, c2(B)->C
    $c1 = Mockery::mock(CriteriaInterface::class);
    $c1->shouldReceive('apply')->once()->with($qbA)->andReturn($qbB);

    $c2 = Mockery::mock(CriteriaInterface::class);
    $c2->shouldReceive('apply')->once()->with($qbB)->andReturn($qbC);

    // terminal get() happens on the final builder C
    $result = new Collection([1, 2, 3]);
    $qbC->shouldReceive('get')->once()->andReturn($result);

    $out = QueryBuilder::for($model)
        ->withCriteria($c1)
        ->addCriteria($c2)
        ->toCollection();

    expect($out)->toBe($result);
});

it('toModel() returns the first model via ModelHandler path', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $found = Mockery::mock(Model::class);
    $builder->shouldReceive('first')->once()->andReturn($found);

    $out = QueryBuilder::for($model)->toModel();

    expect($out)->toBe($found);
});

it('toCollection() returns a Collection via CollectionHandler path', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $rows = new Collection([['id' => 1]]);
    $builder->shouldReceive('get')->once()->andReturn($rows);

    $out = QueryBuilder::for($model)->toCollection();

    expect($out)->toBeInstanceOf(Collection::class)->and($out)->toBe($rows);
});

it('toPaginated() calls paginate(perPage,page) and returns paginator (withArgs pattern)', function () {
    $builder = Mockery::mock(QueryBuilderContract::class);
    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    // paginator REAL para evitar métodos internos no stubbeados
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
        ->withArgs(fn ($perPage, $columns, $pageName, $page) => $perPage === 5 && $page === 1)
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
