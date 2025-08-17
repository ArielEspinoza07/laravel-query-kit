<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelQueryKit\Contracts\CollectionHandlerInterface;
use LaravelQueryKit\Contracts\PaginatedHandlerInterface;
use LaravelQueryKit\Contracts\ResourceCollectionHandlerInterface;
use LaravelQueryKit\Exceptions\HandlerException;
use LaravelQueryKit\Service\QueryService;

final readonly class ResourceCollectionHandler extends Handler implements ResourceCollectionHandlerInterface
{
    public function __construct(
        private PaginatedHandlerInterface|CollectionHandlerInterface $handler,
        QueryService $service,
    ) {
        parent::__construct($service);
    }

    public function get(string $resourceClass): ResourceCollection
    {
        /** @phpstan-ignore-next-line */
        if (! is_subclass_of($resourceClass, ResourceCollection::class)) {
            throw new HandlerException(
                message: sprintf(
                    'Class %s must be an instance of {%s}.',
                    $resourceClass,
                    ResourceCollection::class,
                ),
            );
        }

        return new $resourceClass($this->handler->get());
    }
}
