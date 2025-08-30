<?php

declare(strict_types=1);

namespace LaravelQueryKit\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \LaravelQueryKit\QueryBuilder for(\Illuminate\Database\Eloquent\Model $model)
 * @method static \LaravelQueryKit\QueryBuilder withPagination(int $page, int $perPage)
 * @method static \LaravelQueryKit\QueryBuilder withCriteria(\LaravelQueryKit\Contracts\CriteriaInterface ...$c)
 * @method static \LaravelQueryKit\QueryBuilder setCriteria(array $criteria)
 * @method static \LaravelQueryKit\QueryBuilder addCriteria(\LaravelQueryKit\Contracts\CriteriaInterface $c)
 * @method static \Illuminate\Contracts\Database\Query\Builder builder()
 * @method static \Illuminate\Database\Eloquent\Model|null toModel()
 * @method static \Illuminate\Support\Collection toCollection()
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator toPaginated()
 * @method static \Illuminate\Http\Resources\Json\ResourceCollection toResourceCollection(string $resource)
 * @method static \Illuminate\Http\Resources\Json\JsonResource toJsonResource(string $resource)
 */
final class QueryKitBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'query-kit';
    }
}
