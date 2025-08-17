<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Handlers\Handler;
use LaravelQueryKit\Service\QueryService;

it('static create() returns a base Handler instance bound to the given service', function () {
    $builder = Mockery::mock(Builder::class);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $service = QueryService::make($model);

    $h = Handler::create(service: $service);

    expect($h)->toBeInstanceOf(Handler::class);
});
