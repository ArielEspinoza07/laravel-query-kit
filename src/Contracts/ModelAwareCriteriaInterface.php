<?php

declare(strict_types=1);

namespace LaravelQueryKit\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface ModelAwareCriteriaInterface
 *
 * Represents a criteria that requires awareness of the Eloquent model it operates on.
 * Implementations must provide a way to bind a model instance immutably, ensuring
 * that query logic can be applied in the context of the given model.
 */
interface ModelAwareCriteriaInterface extends CriteriaInterface
{
    /**
     * Bind the given model to the criteria and return a new immutable instance.
     *
     * @param  Model  $model  The Eloquent model to associate with this criteria.
     * @return static A cloned instance of the criteria with the model bound.
     */
    public function withModel(Model $model): self;
}
