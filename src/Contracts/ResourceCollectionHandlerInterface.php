<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Handler that wraps a result set (collection or paginator) into a ResourceCollection.
 */
interface ResourceCollectionHandlerInterface extends HandlerInterface
{
    /**
     * Execute the query and wrap the result set into the given ResourceCollection class.
     * Useful for "index" endpoints returning multiple items, optionally paginated.
     *
     * @param  class-string<ResourceCollection>  $resourceClass  Fully qualified resource-collection class name.
     * @return ResourceCollection The resource collection representing the result set.
     */
    public function get(string $resourceClass): ResourceCollection;
}
