<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Contracts\HandlerInterface;
use LaravelQueryKit\Contracts\ModelHandlerInterface;

it('ModelHandlerInterface extends HandlerInterface and defines get(): ?Model', function () {
    expect(interface_exists(ModelHandlerInterface::class))->toBeTrue();

    $ref = new ReflectionClass(ModelHandlerInterface::class);
    expect($ref->implementsInterface(HandlerInterface::class))->toBeTrue();

    $m = $ref->getMethod('get');
    expect($m->isStatic())->toBeFalse()
        ->and($m->getNumberOfParameters())->toBe(0);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(Model::class)
        ->and($ret->allowsNull())->toBeTrue();
    // ?Model
});
