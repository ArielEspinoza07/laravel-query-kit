<?php

declare(strict_types=1);

namespace LaravelQueryKit\ValueObjects;

final readonly class Pagination
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {}
}
