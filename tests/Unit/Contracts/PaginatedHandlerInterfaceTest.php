<?php

declare(strict_types=1);

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelQueryKit\Contracts\HandlerInterface;
use LaravelQueryKit\Contracts\PaginatedHandlerInterface;

it('PaginatedHandlerInterface extends HandlerInterface and defines get(): LengthAwarePaginator', function () {
    expect(interface_exists(PaginatedHandlerInterface::class))->toBeTrue();

    $ref = new ReflectionClass(PaginatedHandlerInterface::class);
    expect($ref->implementsInterface(HandlerInterface::class))->toBeTrue();

    $m = $ref->getMethod('get');
    expect($m->isStatic())->toBeFalse()
        ->and($m->getNumberOfParameters())->toBe(0);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(LengthAwarePaginator::class)
        ->and($ret->allowsNull())->toBeFalse();
});
