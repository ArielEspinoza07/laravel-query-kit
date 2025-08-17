<?php

declare(strict_types=1);

namespace LaravelQueryKit\Tests\Stubs\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DummyJsonResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['data' => $this->resource];
    }
}
