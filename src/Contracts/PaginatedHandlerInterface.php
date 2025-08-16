<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaginatedHandlerInterface extends HandlerInterface
{
    public function get(): LengthAwarePaginator;
}
