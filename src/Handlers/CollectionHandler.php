<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use Illuminate\Support\Collection;
use LaravelQueryKit\Contracts\CollectionHandlerInterface;

final readonly class CollectionHandler extends Handler implements CollectionHandlerInterface
{
    public function get(): Collection
    {
        $this->service->apply();

        return $this->service->builder()->get();
    }
}
