<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelQueryKit\Contracts\CollectionHandlerInterface;
use LaravelQueryKit\Contracts\PaginatedHandlerInterface;
use LaravelQueryKit\Exceptions\HandlerException;
use LaravelQueryKit\Handlers\ResourceCollectionHandler;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyResourceCollection;

it('throws HandlerException for non resource-collection class', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $service = QueryService::make($model);

    $inner = Mockery::mock(CollectionHandlerInterface::class);

    $handler = new ResourceCollectionHandler(handler: $inner, service: $service);

    $handler->get(stdClass::class);
})->throws(HandlerException::class);

it('delegates to inner handler->get() and wraps into the given ResourceCollection subclass (collection case)', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $service = QueryService::make($model);

    $inner = Mockery::mock(CollectionHandlerInterface::class);

    $payload = collect([1, 2, 3]);
    $inner->shouldReceive('get')->once()->andReturn($payload);

    $handler = new ResourceCollectionHandler(handler: $inner, service: $service);

    $res = $handler->get(DummyResourceCollection::class);

    expect($res)->toBeInstanceOf(DummyResourceCollection::class);
});

it('delegates to inner handler->get() and wraps into the given ResourceCollection subclass (paginator case)', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $service = QueryService::make($model);

    $items = new Collection([['id' => 1], ['id' => 2], ['id' => 3]]);
    $paginator = new \Illuminate\Pagination\LengthAwarePaginator($items, $items->count(), 10, 1);

    $inner = Mockery::mock(PaginatedHandlerInterface::class);
    $inner->shouldReceive('get')->once()->andReturn($paginator);

    $handler = new ResourceCollectionHandler(handler: $inner, service: $service);

    $res = $handler->get(DummyResourceCollection::class);

    expect($res)->toBeInstanceOf(DummyResourceCollection::class);
});
