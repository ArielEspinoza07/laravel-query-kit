<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Handler that wraps a single result into a JsonResource.
 */
interface JsonResourceHandlerInterface extends HandlerInterface
{
    /**
     * Execute the query and wrap the first result into the given JsonResource class.
     * Useful for "show" endpoints returning a single resource.
     *
     * @param  class-string<JsonResource>  $resourceClass  Fully qualified resource class name.
     * @return JsonResource A resource instance representing the first row (or an empty resource by implementation choice).
     */
    public function get(string $resourceClass): JsonResource;
}
