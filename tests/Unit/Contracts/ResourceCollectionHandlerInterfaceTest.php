<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelQueryKit\Contracts\HandlerInterface;
use LaravelQueryKit\Contracts\ResourceCollectionHandlerInterface;

it('ResourceCollectionHandlerInterface extends HandlerInterface and defines get(string $resourceClass): ResourceCollection', function () {
    expect(interface_exists(ResourceCollectionHandlerInterface::class))->toBeTrue();

    $ref = new ReflectionClass(ResourceCollectionHandlerInterface::class);
    expect($ref->implementsInterface(HandlerInterface::class))->toBeTrue();

    $m = $ref->getMethod('get');
    expect($m->isStatic())->toBeFalse();

    $params = $m->getParameters();
    expect($params)->toHaveCount(1)
        ->and($params[0]->getName())->toBe('resourceClass');

    $pt = $params[0]->getType();
    expect($pt)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($pt->getName())->toBe('string');

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(ResourceCollection::class);

    $doc = (string) $m->getDocComment();
    expect($doc)->toContain('class-string<ResourceCollection>');
});
