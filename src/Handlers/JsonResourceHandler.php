<?php

declare(strict_types=1);

namespace LaravelQueryKit\Handlers;

use Illuminate\Http\Resources\Json\JsonResource;
use LaravelQueryKit\Contracts\JsonResourceHandlerInterface;
use LaravelQueryKit\Exceptions\HandlerException;

final readonly class JsonResourceHandler extends Handler implements JsonResourceHandlerInterface
{
    public function get(string $resourceClass): JsonResource
    {
        /** @phpstan-ignore-next-line */
        if (! is_subclass_of($resourceClass, JsonResource::class)) {
            throw new HandlerException(
                message: sprintf(
                    'Class %s must be an instance of {%s}.',
                    $resourceClass,
                    JsonResource::class,
                ),
            );
        }

        $this->service->apply();

        return new $resourceClass($this->service->builder()->first());
    }
}
