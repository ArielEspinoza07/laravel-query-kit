<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Exception;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Contracts\SortCriteriaInterface;
use LaravelQueryKit\Criteria\Sort\BelongsToManyOrderByCriteria;
use LaravelQueryKit\Criteria\Sort\BelongsToOrderByCriteria;
use LaravelQueryKit\Criteria\Sort\HasManyOrderByCriteria;
use LaravelQueryKit\Criteria\Sort\HasManyThroughOrderByCriteria;
use LaravelQueryKit\Criteria\Sort\HasOneOrderByCriteria;
use LaravelQueryKit\Criteria\Sort\HasOneOrManyOrderByCriteria;
use LaravelQueryKit\Criteria\Sort\OrderByCriteria;
use LaravelQueryKit\Exceptions\SortCriteriaException;

final readonly class SortCriteria implements CriteriaInterface
{
    /**
     * @param  array<SortCriteriaInterface>  $sorts
     *
     * @throws Exception
     */
    public function __construct(
        private Model $model,
        private string $column,
        private string $direction = 'asc',
        private array $sorts = [],
    ) {
        if (! in_array($direction, ['asc', 'desc'])) {
            throw new SortCriteriaException(
                message: sprintf('Invalid direction %s', $direction),
            );
        }
    }

    /**
     * @throws Exception
     */
    public function apply(Builder $builder): Builder
    {
        if (! str_contains($this->column, '.')) {
            /** @var OrderByCriteria $handler * */
            $handler = $this->defaultSort();

            return $handler->applyWithoutRelation(
                builder: $builder,
                column: $this->column,
                direction: $this->direction,
            );
        }

        $relations = explode('.', $this->column);
        $column = array_pop($relations);

        $currentModel = $this->model;

        foreach ($relations as $relationName) {
            if (! method_exists($currentModel, $relationName)) {
                throw new SortCriteriaException(
                    message: sprintf('Relation %s not found on model {%s}', $relationName, $currentModel::class),
                );
            }

            $relation = $currentModel->$relationName();
            if (! $relation instanceof Relation) {
                throw new SortCriteriaException(
                    message: sprintf('Method {%s} is not a valid Eloquent relation.', $relationName),
                );
            }

            $sortClass = $this->resolveSort($relation);

            $builder = $sortClass->apply(
                builder: $builder,
                model: $currentModel,
                relation: $relation,
                column: $column,
                direction: $this->direction,
            );

            $currentModel = $relation->getRelated();
        }

        return $builder;
    }

    /**
     * @throws Exception
     */
    public function addSort(SortCriteriaInterface $sort): self
    {
        $sorts = $this->sorts;
        $sorts[] = $sort;

        return new self(
            model: $this->model,
            column: $this->column,
            direction: $this->direction,
            sorts: $sorts,
        );
    }

    /**
     * @param  array<SortCriteriaInterface>  $sorts
     *
     * @throws Exception
     */
    public function setSorts(array $sorts): self
    {
        return new self(
            model: $this->model,
            column: $this->column,
            direction: $this->direction,
            sorts: $sorts,
        );
    }

    /**
     * @throws Exception
     */
    public function withSorts(SortCriteriaInterface ...$sort): self
    {
        $sorts = $this->sorts;
        array_push($sorts, ...$sort);

        return new self(
            model: $this->model,
            column: $this->column,
            direction: $this->direction,
            sorts: $sorts,
        );
    }

    /**
     * @throws Exception
     */
    public function withDefaultSorts(): self
    {
        return new self(
            model: $this->model,
            column: $this->column,
            direction: $this->direction,
            sorts: [
                new OrderByCriteria,
                new BelongsToManyOrderByCriteria,
                new BelongsToOrderByCriteria,
                new HasManyOrderByCriteria,
                new HasManyThroughOrderByCriteria,
                new HasOneOrderByCriteria,
                new HasOneOrManyOrderByCriteria,
            ],
        );
    }

    private function defaultSort(): SortCriteriaInterface
    {
        foreach ($this->sorts as $sort) {
            if ($sort instanceof OrderByCriteria) {
                return $sort;
            }
        }

        throw new SortCriteriaException('No default sort criteria registered.');
    }

    /**
     * @throws Exception
     */
    private function resolveSort(Relation $relation): SortCriteriaInterface
    {
        foreach ($this->sorts as $sort) {
            if ($sort->supports($relation)) {
                return $sort;
            }
        }

        throw new SortCriteriaException(
            message: sprintf('Unsupported relation type: %s', get_class($relation)),
        );
    }
}
