<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelQueryKit\Contracts\CollectionHandlerInterface;
use LaravelQueryKit\Contracts\HandlerInterface;
use LaravelQueryKit\Contracts\PaginatedHandlerInterface;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\ValueObjects\Pagination;

final class HandlerFactory
{
    /**
     * @param  class-string<ResourceCollection|JsonResource>|null  $resource
     */
    public static function make(QueryService $service, ?Pagination $pagination = null, ?string $resource = null): HandlerInterface
    {
        if ($resource) {
            return self::makeResourceOrResourceCollection(service: $service, resource: $resource, pagination: $pagination);
        }

        return self::makeCollectionOrPaginated(service: $service, pagination: $pagination);
    }

    private static function makeCollectionOrPaginated(QueryService $service, ?Pagination $pagination = null): HandlerInterface
    {
        if ($pagination) {
            return new PaginatedHandler(pagination: $pagination, service: $service);
        }

        return new CollectionHandler(service: $service);
    }

    /**
     * @param  class-string<ResourceCollection|JsonResource>  $resource
     */
    private static function makeResourceOrResourceCollection(QueryService $service, string $resource, ?Pagination $pagination = null): HandlerInterface
    {
        if (is_subclass_of($resource, ResourceCollection::class)) {
            /** @var PaginatedHandlerInterface|CollectionHandlerInterface $handler */
            $handler = self::makeCollectionOrPaginated(service: $service, pagination: $pagination);

            return new ResourceCollectionHandler(
                handler: $handler,
                service: $service,
            );
        }

        return new JsonResourceHandler(service: $service);
    }
}
