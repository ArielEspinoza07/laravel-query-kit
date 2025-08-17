<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use LaravelQueryKit\Contracts\HandlerInterface;
use LaravelQueryKit\Service\QueryService;

readonly class Handler implements HandlerInterface
{
    public function __construct(
        protected QueryService $service,
    ) {}

    public static function create(QueryService $service): self
    {
        return new self(service: $service);
    }
}
