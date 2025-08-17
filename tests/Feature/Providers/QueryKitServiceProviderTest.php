<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use LaravelQueryKit\Providers\QueryKitServiceProvider;
use LaravelQueryKit\QueryBuilder;

it('binds the query-kit singleton to QueryBuilder', function () {
    expect($this->app->bound('query-kit'))->toBeTrue();

    $a = $this->app->make('query-kit');
    $b = $this->app->make('query-kit');

    expect($a)->toBeInstanceOf(QueryBuilder::class)
        ->and(spl_object_id($a))->toBe(spl_object_id($b));
});

it('registers the make:criteria command in console', function () {
    $all = Artisan::all();

    expect($all)->toHaveKey('make:criteria');
});

it('the provider class is final and in the expected namespace', function () {
    $ref = new ReflectionClass(QueryKitServiceProvider::class);

    expect($ref->isFinal())->toBeTrue()
        ->and($ref->getNamespaceName())->toBe('LaravelQueryKit\\Providers');
});
