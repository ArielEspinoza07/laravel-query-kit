<?php

declare(strict_types=1);

namespace LaravelQueryKit\Criteria;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Exceptions\CriteriaException;

final class WithTrashedCriteria implements CriteriaInterface
{
    public function __construct(private bool $active = false) {}

    /**
     * @throws CriteriaException
     */
    public function apply(Builder $builder): Builder
    {
        if (! $builder instanceof EloquentBuilder) {
            throw new CriteriaException('WithTrashedCriteria requires an Eloquent builder.');
        }

        $model = $builder->getModel();

        if (! $this->useSoftDeleteTrait($model)) {
            throw new CriteriaException(sprintf(
                'Model [%s] does not use the SoftDeletes trait.',
                $model::class
            ));
        }

        if ($this->active) {
            /** @phpstan-ignore-next-line */
            return $builder->withTrashed();
        }

        return $builder;
    }

    private function useSoftDeleteTrait(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model), true);
    }
}
