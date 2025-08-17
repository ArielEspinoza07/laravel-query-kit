<?php

declare(strict_types=1);

namespace LaravelQueryKit\Tests\Stubs\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class DummyResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return ['data' => $this->collection];
    }
}
