<?php

declare(strict_types=1);

use LaravelQueryKit\Contracts\HandlerInterface;
use LaravelQueryKit\Service\QueryService;

it('defines HandlerInterface::create(QueryService): self as static', function () {
    expect(interface_exists(HandlerInterface::class))->toBeTrue();

    $ref = new ReflectionClass(HandlerInterface::class);
    $m = $ref->getMethod('create');

    expect($m->isStatic())->toBeTrue();

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe('self');

    $params = $m->getParameters();
    expect($params)->toHaveCount(1)
        ->and($params[0]->getName())->toBe('service');

    $pt = $params[0]->getType();
    expect($pt)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($pt->getName())->toBe(QueryService::class);
});
