<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Contracts\ModelHandlerInterface;

final readonly class ModelHandler extends Handler implements ModelHandlerInterface
{
    public function get(): ?Model
    {
        $this->service->apply();

        return $this->service->builder()->first();
    }
}
