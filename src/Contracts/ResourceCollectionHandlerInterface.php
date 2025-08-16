<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Http\Resources\Json\ResourceCollection;

interface ResourceCollectionHandlerInterface extends HandlerInterface
{
    /**
     * Transforms query results using a Laravel resource.
     *
     * @param  class-string<ResourceCollection>  $resourceClass  A resource instance (e.g., UserCollection::class)
     */
    public function get(string $resourceClass): ResourceCollection;
}
