<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelQueryKit\Contracts\PaginatedHandlerInterface;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\ValueObjects\Pagination;

final readonly class PaginatedHandler extends Handler implements PaginatedHandlerInterface
{
    public function __construct(
        private Pagination $pagination,
        QueryService $service,
    ) {
        parent::__construct($service);
    }

    public function get(): LengthAwarePaginator
    {
        $this->service->apply();

        return $this->service
            ->builder()
            ->paginate(
                perPage: $this->pagination->perPage,
                page: $this->pagination->page,
            );
    }
}
