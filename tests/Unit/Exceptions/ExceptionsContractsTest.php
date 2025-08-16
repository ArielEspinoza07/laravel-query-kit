<?php

declare(strict_types=1);

use LaravelQueryKit\Exceptions\CriteriaException;
use LaravelQueryKit\Exceptions\HandlerException;
use LaravelQueryKit\Exceptions\QueryBuilderException;
use LaravelQueryKit\Exceptions\QueryServiceException;
use LaravelQueryKit\Exceptions\SortCriteriaException;

dataset('exceptions', [
    [CriteriaException::class, Exception::class],
    [HandlerException::class, InvalidArgumentException::class],
    [QueryBuilderException::class, InvalidArgumentException::class],
    [QueryServiceException::class, LogicException::class],
    [SortCriteriaException::class, InvalidArgumentException::class],
]);

it('class exists and is final', function (string $fqcn) {
    expect(class_exists($fqcn))->toBeTrue();

    $ref = new ReflectionClass($fqcn);
    expect($ref->isFinal())->toBeTrue();
})->with('exceptions');

it('inherits from the expected parent and base Exception', function (string $fqcn, string $parent) {
    $e = new $fqcn('x');

    expect($e)->toBeInstanceOf($parent);

    expect($e)->toBeInstanceOf(Exception::class);
})->with('exceptions');

it('propagates message, code and previous', function (string $fqcn) {
    $prev = new RuntimeException('prev');
    $ex   = new $fqcn('boom', 123, $prev);

    expect($ex->getMessage())->toBe('boom')
        ->and($ex->getCode())->toBe(123)
        ->and($ex->getPrevious())->toBe($prev);
})->with('exceptions');
