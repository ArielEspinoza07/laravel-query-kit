<?php

declare(strict_types=1);

use LaravelQueryKit\ValueObjects\Pagination;

dataset('invalidInt', [
    'string' => '1',
    'float' => 1.5,
    'null' => null,
    'true' => true,
    'false' => false,
    'array' => [],
]);

it('constructs with ints (positional) and exposes values', function () {
    $p = new Pagination(2, 50);

    expect($p->page)->toBe(2)
        ->and($p->perPage)->toBe(50);
});

it('constructs with ints (named args) and exposes values', function () {
    $p = new Pagination(page: 1, perPage: 15);

    expect($p->page)->toBe(1)
        ->and($p->perPage)->toBe(15);
});

it('throws TypeError when page is not an int', function ($bad) {
    new Pagination($bad, 20);
})->with('invalidInt')->throws(TypeError::class);

it('throws TypeError when perPage is not an int', function ($bad) {
    new Pagination(1, $bad);
})->with('invalidInt')->throws(TypeError::class);

it('is immutable (readonly) and cannot be modified', function () {
    $p = new Pagination(3, 25);

    $p->page = 4;
})->throws(Error::class, 'Cannot modify readonly property');

it('requires both arguments', function () {
    new Pagination;
})->throws(ArgumentCountError::class);
