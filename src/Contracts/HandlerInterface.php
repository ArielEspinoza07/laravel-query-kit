<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use LaravelQueryKit\Service\QueryService;

interface HandlerInterface
{
    public static function create(QueryService $service): self;
}
