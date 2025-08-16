<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Support\Collection;

interface CollectionHandlerInterface extends HandlerInterface
{
    public function get(): Collection;
}
