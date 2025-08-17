<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaravelQueryKit\Handlers\CollectionHandler;
use LaravelQueryKit\Service\QueryService;

it('calls service->apply() then builder()->get() and returns the collection', function () {
    $result = new Collection([1, 2, 3]);

    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('get')->once()->andReturn($result);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')->once()->andReturn($builder);

    $service = QueryService::make($model);

    $handler = new CollectionHandler(service: $service);

    $out = $handler->get();

    expect($out)->toBeInstanceOf(Collection::class)
        ->and($out)->toBe($result);
});
