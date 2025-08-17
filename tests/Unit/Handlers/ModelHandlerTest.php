<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use LaravelQueryKit\Handlers\ModelHandler;
use LaravelQueryKit\Service\QueryService;

it('calls service->apply() then builder()->first() and returns the model (or null)', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $builder->shouldReceive('first')->once()->andReturn($model);

    $service = QueryService::make($model);

    $handler = new ModelHandler(service: $service);

    $out = $handler->get();

    expect($out)->toBe($model);
});
