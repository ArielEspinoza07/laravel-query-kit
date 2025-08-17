<?php

declare(strict_types=1);

namespace LaravelQueryKit\Service;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Contracts\ModelAwareCriteriaInterface;
use LaravelQueryKit\Exceptions\QueryServiceException;

/**
 * Service that orchestrates a Model, a Query Builder and a set of Criteria.
 *
 * It provides a small, fluent API to collect criteria and then apply them
 * (in insertion order) to the underlying query builder. Instances must be
 * created via {@see QueryService::make()} to ensure the service is "ready".
 *
 * Notes:
 * - The internal builder is typically an Eloquent\Builder instance obtained
 *   from Model::newQuery(), but it is exposed through the Query\Builder
 *   contract for portability.
 * - This service is stateful: calling {@see apply()} mutates the internal
 *   builder reference as each criterion returns its transformed builder.
 *
 * @see CriteriaInterface
 */
final class QueryService
{
    /**
     * @param  array<CriteriaInterface>  $criteria
     */
    private function __construct(
        private array $criteria,
        private readonly ?Model $model,
        private ?Builder $builder,
    ) {}

    /**
     * Factory that boots a ready-to-use service for the given model.
     *
     * It will initialize the internal query builder via {@see Model::newQuery()}
     * and start with an empty criteria list.
     *
     * @return self Ready service instance bound to the provided model.
     */
    public static function make(Model $model): self
    {
        return new self(
            criteria: [],
            model: $model,
            builder: $model->newQuery(),
        );
    }

    /**
     * Append one or more criteria to the current list (preserving order).
     *
     * This mutates the service in-place and returns the same instance so you
     * can fluently chain calls.
     *
     * @param  CriteriaInterface  ...$c  One or more criteria to enqueue.
     * @return self The same service instance (for chaining).
     */
    public function withCriteria(CriteriaInterface ...$c): self
    {
        array_push($this->criteria, ...$c);

        return $this;
    }

    /**
     * Replace the entire criteria list with the provided array.
     *
     * This mutates the service in-place and returns the same instance so you
     * can fluently chain calls.
     *
     * @param  array<CriteriaInterface>  $criteria  Criteria to set (in order).
     * @return self The same service instance (for chaining).
     */
    public function setCriteria(array $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Append a single criterion to the current list.
     *
     * This mutates the service in-place and returns the same instance so you
     * can fluently chain calls.
     *
     * @return self The same service instance (for chaining).
     */
    public function addCriteria(CriteriaInterface $c): self
    {
        $this->criteria[] = $c;

        return $this;
    }

    /**
     * Apply the queued criteria to the underlying builder in insertion order.
     *
     * Each criterion receives the current builder and must return a (possibly
     * transformed) builder. The internal builder reference is updated after
     * each step. This method does not execute the query; it only composes it.
     *
     * @throws QueryServiceException If the service is not ready (make() was not called).
     */
    public function apply(): void
    {
        $this->ensureReady();
        foreach ($this->criteria as $criteria) {
            if ($criteria instanceof ModelAwareCriteriaInterface) {
                $criteria = $criteria->withModel($this->model);
            }
            $this->builder = $criteria->apply(builder: $this->builder);
        }
    }

    /**
     * Get the underlying Eloquent model instance.
     *
     *
     * @throws QueryServiceException If the model is not initialized (make() not called).
     */
    public function model(): Model
    {
        if (! $this->hasModel()) {
            throw new QueryServiceException('Model is not initialized. Call make() first.');
        }

        return $this->model;
    }

    /**
     * Get the underlying query builder instance.
     *
     * While type-hinted as {@see Builder} for portability, this will typically
     * be an Eloquent builder created via {@see Model::newQuery()}.
     *
     *
     * @throws QueryServiceException If the builder is not initialized (make() not called).
     */
    public function builder(): Builder
    {
        if (! $this->hasBuilder()) {
            throw new QueryServiceException('Builder is not initialized. Call make() first.');
        }

        return $this->builder;
    }

    /**
     * Get a snapshot of the current criteria queue (in order).
     *
     * @return array<CriteriaInterface>
     */
    public function criteria(): array
    {
        return $this->criteria;
    }

    /**
     * Determine whether the service is ready to operate.
     *
     * A service is considered "ready" when both a model and a builder are set,
     * which is guaranteed after calling {@see QueryService::make()}.
     */
    public function isReady(): bool
    {
        return $this->hasModel() && $this->hasBuilder();
    }

    private function hasModel(): bool
    {
        return isset($this->model);
    }

    private function hasBuilder(): bool
    {
        return isset($this->builder);
    }

    private function ensureReady(): void
    {
        if (! $this->isReady()) {
            throw new QueryServiceException('You must call make() first.');
        }
    }
}
