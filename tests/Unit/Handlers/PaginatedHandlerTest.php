<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Handlers\PaginatedHandler;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\ValueObjects\Pagination;

it('calls service->apply() then builder()->paginate(perPage,page) and returns paginator', function () {
    $pagination = new Pagination(page: 1, perPage: 10);

    $paginator = Mockery::mock(LengthAwarePaginator::class);

    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('paginate')
        ->once()
        ->withArgs(function ($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null, $total = null) use ($pagination) {
            return $perPage === $pagination->perPage
                && $page === $pagination->page;
        })
        ->andReturn($paginator);

    $model = Mockery::mock(Model::class);
    $model->shouldReceive('newQuery')
        ->once()
        ->andReturn($builder);

    $service = QueryService::make($model);

    $handler = new PaginatedHandler(pagination: $pagination, service: $service);

    $out = $handler->get();

    expect($out)->toBeInstanceOf(LengthAwarePaginator::class);
});
