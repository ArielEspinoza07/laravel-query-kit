<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Handlers\CollectionHandler;
use LaravelQueryKit\Handlers\HandlerFactory;
use LaravelQueryKit\Handlers\JsonResourceHandler;
use LaravelQueryKit\Handlers\PaginatedHandler;
use LaravelQueryKit\Handlers\ResourceCollectionHandler;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyJsonResource as HF_DummyJsonResource;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyResourceCollection as HF_DummyResourceCollection;
use LaravelQueryKit\ValueObjects\Pagination;

beforeEach(function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $this->service = QueryService::make($model);
});

it('make() returns CollectionHandler when no pagination and no resource provided', function () {
    $handler = HandlerFactory::make(service: $this->service);

    expect($handler)->toBeInstanceOf(CollectionHandler::class);
});

it('make() returns PaginatedHandler when pagination provided and no resource', function () {
    $pagination = new Pagination(page: 1, perPage: 15);

    $handler = HandlerFactory::make(service: $this->service, pagination: $pagination);

    expect($handler)->toBeInstanceOf(PaginatedHandler::class);
});

it('make() returns JsonResourceHandler when JsonResource subclass provided', function () {
    $handler = HandlerFactory::make(service: $this->service, resource: HF_DummyJsonResource::class);

    expect($handler)->toBeInstanceOf(JsonResourceHandler::class);
});

it('make() returns ResourceCollectionHandler when ResourceCollection subclass provided (no pagination)', function () {
    $handler = HandlerFactory::make(service: $this->service, resource: HF_DummyResourceCollection::class);

    expect($handler)->toBeInstanceOf(ResourceCollectionHandler::class);
});

it('make() returns ResourceCollectionHandler when ResourceCollection subclass provided (with pagination)', function () {
    $pagination = new Pagination(page: 2, perPage: 10);

    $handler = HandlerFactory::make(service: $this->service, resource: HF_DummyResourceCollection::class, pagination: $pagination);

    expect($handler)->toBeInstanceOf(ResourceCollectionHandler::class);
});
