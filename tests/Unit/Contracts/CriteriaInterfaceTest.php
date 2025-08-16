<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

it('CriteriaInterface defines apply(Builder): Builder', function () {
    expect(interface_exists(CriteriaInterface::class))->toBeTrue();

    $ref = new ReflectionClass(CriteriaInterface::class);
    $m = $ref->getMethod('apply');

    expect($m->isStatic())->toBeFalse();

    $params = $m->getParameters();
    expect($params)->toHaveCount(1)
        ->and($params[0]->getName())->toBe('builder');

    $pt = $params[0]->getType();
    expect($pt)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($pt->getName())->toBe(Builder::class);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(Builder::class);
});
