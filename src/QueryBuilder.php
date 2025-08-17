<?php

declare(strict_types=1);

namespace LaravelQueryKit;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use LaravelQueryKit\Contracts\CollectionHandlerInterface;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Contracts\PaginatedHandlerInterface;
use LaravelQueryKit\Contracts\ResourceCollectionHandlerInterface;
use LaravelQueryKit\Exceptions\QueryBuilderException;
use LaravelQueryKit\Handlers\HandlerFactory;
use LaravelQueryKit\Handlers\JsonResourceHandler;
use LaravelQueryKit\Handlers\ModelHandler;
use LaravelQueryKit\Service\QueryService;
use LaravelQueryKit\ValueObjects\Pagination;

/**
 * Small fluent facade over QueryService to compose queries with Criteria
 * and materialize results through the appropriate handlers.
 *
 * This class is immutable: configuration methods (e.g. withPagination/withCriteria)
 * return a new instance while keeping the underlying QueryService up to date.
 */
final readonly class QueryBuilder
{
    private ?Pagination $pagination;

    private ?QueryService $service;

    /**
     * Create a new builder wrapper.
     *
     * Prefer using {@see QueryBuilder::for()} to bootstrap from a Model. The
     * public constructor exists for advanced scenarios / testing where you
     * need to inject a prepared QueryService and/or Pagination.
     */
    public function __construct(?Pagination $pagination = null, ?QueryService $service = null)
    {
        $this->pagination = $pagination;
        $this->service = $service;
    }

    /**
     * Factory that boots the builder for a given Eloquent model.
     *
     * Internally calls {@see Model::newQuery()} to initialize the QueryService.
     *
     * @return self New immutable instance bound to the model's query.
     */
    public static function for(Model $model): self
    {
        return new self(
            service: QueryService::make(model: $model),
        );
    }

    /**
     * Set (or replace) pagination settings.
     *
     * Returns a new immutable instance with pagination preserved for subsequent
     * materialization methods like {@see toPaginated()} or {@see toResourceCollection()}.
     */
    public function withPagination(int $page, int $perPage): self
    {
        return new self(
            pagination: new Pagination(
                page: $page,
                perPage: $perPage,
            ),
            service: $this->service,
        );
    }

    /**
     * Append one or more criteria to the underlying QueryService.
     *
     * Criteria are collected and only applied when a terminal method
     * (e.g., toModel/toCollection/toPaginated/toJsonResource/toResourceCollection) is called.
     *
     * @return self New immutable instance with updated criteria.
     */
    public function withCriteria(CriteriaInterface ...$c): self
    {
        return new self(
            pagination: $this->pagination,
            service: $this->service->withCriteria(...$c),
        );
    }

    /**
     * Replace the entire criteria list.
     *
     * @param  array<CriteriaInterface>  $criteria  Criteria to set, in order.
     * @return self New immutable instance with updated criteria.
     */
    public function setCriteria(array $criteria): self
    {
        return new self(
            pagination: $this->pagination,
            service: $this->service->setCriteria($criteria),
        );
    }

    /**
     * Append a single criterion to the end of the list.
     *
     * @return self New immutable instance with updated criteria.
     */
    public function addCriteria(CriteriaInterface $c): self
    {
        return new self(
            pagination: $this->pagination,
            service: $this->service->addCriteria($c),
        );
    }

    /**
     * Expose the underlying query builder for advanced composition.
     *
     * Usually you won't need this because terminal methods (toModel/…)
     * will execute the query via handlers after applying all criteria.
     *
     * @return Builder The current query builder from the QueryService.
     */
    public function builder(): Builder
    {
        return $this->service->builder();
    }

    /**
     * Materialize the first matching model after applying all criteria.
     *
     * @return Model|null The first model or null if no rows match.
     */
    public function toModel(): ?Model
    {
        return (new ModelHandler(service: $this->service))->get();
    }

    /**
     * Materialize the full result set as a Laravel Collection.
     *
     * @return Collection<array-key, mixed>
     */
    public function toCollection(): Collection
    {
        /** @var CollectionHandlerInterface $handler */
        $handler = HandlerFactory::make(
            service: $this->service,
        );

        return $handler->get();
    }

    /**
     * Materialize a length-aware paginator.
     *
     * @throws QueryBuilderException When pagination is not configured yet.
     */
    public function toPaginated(): LengthAwarePaginator
    {
        if (! $this->pagination) {
            throw new QueryBuilderException('Pagination fields are not initialized. Call withPagination() first.');
        }

        /** @var PaginatedHandlerInterface $handler */
        $handler = HandlerFactory::make(
            service: $this->service,
            pagination: $this->pagination,
        );

        return $handler->get();
    }

    /**
     * Materialize a result set wrapped in a ResourceCollection.
     *
     * If pagination was configured via {@see withPagination()}, the inner handler
     * will paginate the query before wrapping the results.
     *
     * @param  class-string<ResourceCollection>  $resource  Fully-qualified ResourceCollection class name.
     */
    public function toResourceCollection(string $resource): ResourceCollection
    {
        /** @var ResourceCollectionHandlerInterface $handler */
        $handler = HandlerFactory::make(
            service: $this->service,
            pagination: $this->pagination,
            resource: $resource,
        );

        return $handler->get($resource);
    }

    /**
     * Materialize a single result wrapped in a JsonResource.
     *
     * @param  class-string<JsonResource>  $resource  Fully-qualified JsonResource class name.
     */
    public function toJsonResource(string $resource): JsonResource
    {
        /** @var JsonResourceHandler $handler */
        $handler = HandlerFactory::make(
            service: $this->service,
            resource: $resource,
        );

        return $handler->get($resource);
    }
}
