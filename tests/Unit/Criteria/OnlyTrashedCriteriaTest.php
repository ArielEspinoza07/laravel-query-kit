<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Criteria\OnlyTrashedCriteria;
use LaravelQueryKit\Exceptions\CriteriaException;

it('throws if builder is not Eloquent', function () {
    $qb = Mockery::mock(\Illuminate\Contracts\Database\Query\Builder::class);
    $criteria = new OnlyTrashedCriteria;

    $criteria->apply($qb);
})->throws(CriteriaException::class, 'requires an Eloquent builder');

it('throws if model does not use SoftDeletes', function () {
    $model = Mockery::mock(Model::class);

    $eloquent = Mockery::mock(EloquentBuilder::class);
    $eloquent->shouldReceive('getModel')->andReturn($model);

    $criteria = new OnlyTrashedCriteria;
    $criteria->apply($eloquent);
})->throws(CriteriaException::class, 'does not use the SoftDeletes');

it('calls onlyTrashed when model uses SoftDeletes', function () {
    // Fake model with SoftDeletes
    $model = new class extends Model
    {
        use \Illuminate\Database\Eloquent\SoftDeletes;

        protected $table = 't';
    };

    $eloquent = Mockery::mock(EloquentBuilder::class);
    $eloquent->shouldReceive('getModel')->andReturn($model);
    $eloquent->shouldReceive('onlyTrashed')->once()->andReturnSelf();

    $criteria = new OnlyTrashedCriteria(active: true);
    $result = $criteria->apply($eloquent);

    expect($result)->toBe($eloquent);
});
