<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ModelHandlerInterface extends HandlerInterface
{
    public function get(): ?Model;
}
