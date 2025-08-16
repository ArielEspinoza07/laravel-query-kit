<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use LaravelQueryKit\Contracts\CollectionHandlerInterface;
use LaravelQueryKit\Contracts\HandlerInterface;

it('CollectionHandlerInterface extends HandlerInterface and defines get(): Collection', function () {
    expect(interface_exists(CollectionHandlerInterface::class))->toBeTrue();

    $ref = new ReflectionClass(CollectionHandlerInterface::class);

    expect($ref->implementsInterface(HandlerInterface::class))->toBeTrue();

    $m = $ref->getMethod('get');
    expect($m->isStatic())->toBeFalse()
        ->and($m->getNumberOfParameters())->toBe(0);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(Collection::class)
        ->and($ret->allowsNull())->toBeFalse();
});
