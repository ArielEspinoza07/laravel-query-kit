<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Exceptions\HandlerException;
use LaravelQueryKit\Handlers\JsonResourceHandler;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\Tests\Stubs\Http\Resources\DummyJsonResource;

it('throws HandlerException for non-resource class', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $service = QueryService::make($model);

    $handler = new JsonResourceHandler(service: $service);

    $handler->get(stdClass::class);
})->throws(HandlerException::class);

it('wraps first() result into the given JsonResource subclass', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $builder->shouldReceive('first')->once()->andReturn($model);

    $service = QueryService::make($model);

    $handler = new JsonResourceHandler(service: $service);

    $res = $handler->get(DummyJsonResource::class);

    expect($res)->toBeInstanceOf(DummyJsonResource::class);
});
