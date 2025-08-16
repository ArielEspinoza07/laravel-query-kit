<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Http\Resources\Json\JsonResource;

interface JsonResourceHandlerInterface extends HandlerInterface
{
    /**
     * Transforms query results using a Laravel resource.
     *
     * @param  class-string<JsonResource>  $resourceClass  A resource instance (e.g., UserResource::class)
     */
    public function get(string $resourceClass): JsonResource;
}
