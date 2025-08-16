<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\SortCriteriaInterface;

it('SortCriteriaInterface defines apply(Builder, Model, Relation, string, string): Builder', function () {
    expect(interface_exists(SortCriteriaInterface::class))->toBeTrue();

    $ref = new ReflectionClass(SortCriteriaInterface::class);
    $m   = $ref->getMethod('apply');

    expect($m->isStatic())->toBeFalse();

    $params = $m->getParameters();
    expect($params)->toHaveCount(5);

    [$p0,$p1,$p2,$p3,$p4] = $params;

    expect($p0->getName())->toBe('builder')
        ->and($p1->getName())->toBe('model')
        ->and($p2->getName())->toBe('relation')
        ->and($p3->getName())->toBe('column')
        ->and($p4->getName())->toBe('direction')
        ->and($p0->getType())->toBeInstanceOf(ReflectionNamedType::class)
        ->and($p0->getType()->getName())->toBe(Builder::class)
        ->and($p1->getType())->toBeInstanceOf(ReflectionNamedType::class)
        ->and($p1->getType()->getName())->toBe(Model::class)
        ->and($p2->getType())->toBeInstanceOf(ReflectionNamedType::class)
        ->and($p2->getType()->getName())->toBe(Relation::class)
        ->and($p3->getType())->toBeInstanceOf(ReflectionNamedType::class)
        ->and($p3->getType()->getName())->toBe('string')
        ->and($p4->getType())->toBeInstanceOf(ReflectionNamedType::class)
        ->and($p4->getType()->getName())->toBe('string');

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(Builder::class);
});

it('SortCriteriaInterface defines supports(Relation): bool', function () {
    $ref = new ReflectionClass(SortCriteriaInterface::class);
    $m   = $ref->getMethod('supports');

    expect($m->isStatic())->toBeFalse();

    $params = $m->getParameters();
    expect($params)->toHaveCount(1)
        ->and($params[0]->getName())->toBe('relation');

    $pt = $params[0]->getType();
    expect($pt)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($pt->getName())->toBe(Relation::class);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe('bool');
});
