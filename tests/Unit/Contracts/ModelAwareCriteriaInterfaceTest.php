<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Contracts\ModelAwareCriteriaInterface;

it('ModelAwareCriteriaInterface defines apply(Builder): Builder', function () {
    expect(interface_exists(ModelAwareCriteriaInterface::class))->toBeTrue();

    $ref = new ReflectionClass(ModelAwareCriteriaInterface::class);
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

it('ModelAwareCriteriaInterface defines withModel(Model): self', function () {
    expect(interface_exists(ModelAwareCriteriaInterface::class))->toBeTrue();

    $ref = new ReflectionClass(ModelAwareCriteriaInterface::class);
    $m = $ref->getMethod('withModel');

    expect($m->isStatic())->toBeFalse();

    $params = $m->getParameters();
    expect($params)->toHaveCount(1)
        ->and($params[0]->getName())->toBe('model');

    $pt = $params[0]->getType();
    expect($pt)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($pt->getName())->toBe(Model::class);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe('self');
});
