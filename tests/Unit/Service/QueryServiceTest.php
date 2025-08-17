<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder as QueryBuilderContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use LaravelQueryKit\Contracts\CriteriaInterface;
use LaravelQueryKit\Exceptions\QueryServiceException;
use LaravelQueryKit\Service\QueryService;

it('is final and has a private constructor', function () {
    $ref = new ReflectionClass(QueryService::class);

    expect($ref->isFinal())->toBeTrue();

    $ctor = $ref->getConstructor();
    expect($ctor)->not->toBeNull()
        ->and($ctor->isPrivate())->toBeTrue();
});

it('make(Model) returns a ready service with model and builder coming from newQuery()', function () {
    $qb = Mockery::mock(EloquentBuilder::class);

    $model = new class extends Model
    {
        protected $table = 'tests';

        /** @var EloquentBuilder|null */
        protected $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }

        public function setQb($qb): void
        {
            $this->qb = $qb;
        }
    };
    $model->setQb($qb);

    $svc = QueryService::make($model);

    expect($svc->isReady())->toBeTrue()
        ->and($svc->model())->toBe($model)
        ->and($svc->builder())->toBe($qb)
        ->and($svc->criteria())->toBeArray()->toHaveCount(0);
});

it('withCriteria appends criteria in-place and returns the same service instance', function () {
    $qb = Mockery::mock(EloquentBuilder::class);
    $model = new class extends Model
    {
        protected $table = 'tests';

        public $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }
    };
    $model->qb = $qb;

    $svc = QueryService::make($model);

    $c1 = Mockery::mock(CriteriaInterface::class);
    $c2 = Mockery::mock(CriteriaInterface::class);

    $returned = $svc->withCriteria($c1, $c2);

    expect($returned)->toBe($svc)
        ->and($svc->criteria())->toBe([$c1, $c2]);

    $c3 = Mockery::mock(CriteriaInterface::class);
    $svc->withCriteria($c3);

    expect($svc->criteria())->toBe([$c1, $c2, $c3]);
});

it('setCriteria replaces the whole set; addCriteria appends one item', function () {
    $qb = Mockery::mock(EloquentBuilder::class);
    $model = new class extends Model
    {
        protected $table = 'tests';

        public $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }
    };
    $model->qb = $qb;

    $svc = QueryService::make($model);

    $a = Mockery::mock(CriteriaInterface::class);
    $b = Mockery::mock(CriteriaInterface::class);
    $c = Mockery::mock(CriteriaInterface::class);

    $svc->setCriteria([$a]);
    expect($svc->criteria())->toBe([$a]);

    $svc->setCriteria([$b, $c]);
    expect($svc->criteria())->toBe([$b, $c]);

    $svc->addCriteria($a);
    expect($svc->criteria())->toBe([$b, $c, $a]);
});

it('apply() calls each criterion in order and updates the builder reference', function () {
    // Builder "A" (inicial) y "B" (devuelto por primer criterio), "C" (segundo criterio)
    $qbA = Mockery::mock(EloquentBuilder::class);
    $qbB = Mockery::mock(EloquentBuilder::class);
    $qbC = Mockery::mock(EloquentBuilder::class);

    $model = new class extends Model
    {
        protected $table = 'tests';

        public $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }
    };
    $model->qb = $qbA;

    $svc = QueryService::make($model);

    // Criterios: el primero recibe A y devuelve B; el segundo recibe B y devuelve C
    $c1 = Mockery::mock(CriteriaInterface::class);
    $c1->shouldReceive('apply')->once()
        ->with($qbA)->andReturn($qbB);

    $c2 = Mockery::mock(CriteriaInterface::class);
    $c2->shouldReceive('apply')->once()
        ->with($qbB)->andReturn($qbC);

    $svc->setCriteria([$c1, $c2]);

    // Ejecuta
    $svc->apply();

    // El builder actual debe ser C
    expect($svc->builder())->toBe($qbC);
});

it('model() and builder() throw if service is not ready (ensureReady)', function () {
    $ref = new ReflectionClass(QueryService::class);
    /** @var QueryService $svc */
    $svc = $ref->newInstanceWithoutConstructor();

    expect($svc->isReady())->toBeFalse();

    // model()
    $svc->model();
})->throws(QueryServiceException::class, 'Model is not initialized. Call make() first.');

it('builder() throws if service is not ready', function () {
    $ref = new ReflectionClass(QueryService::class);
    /** @var QueryService $svc */
    $svc = $ref->newInstanceWithoutConstructor();

    $svc->builder();
})->throws(QueryServiceException::class, 'Builder is not initialized. Call make() first.');

it('apply() throws if service is not ready', function () {
    $ref = new ReflectionClass(QueryService::class);
    /** @var QueryService $svc */
    $svc = $ref->newInstanceWithoutConstructor();

    $svc->apply();
})->throws(QueryServiceException::class, 'You must call make() first.');

it('isReady() reflects hasModel() && hasBuilder()', function () {
    // not ready
    $ref = new ReflectionClass(QueryService::class);
    /** @var QueryService $svc */
    $svc = $ref->newInstanceWithoutConstructor();
    expect($svc->isReady())->toBeFalse();

    // ready via make()
    $qb = Mockery::mock(EloquentBuilder::class);
    $model = new class extends Model
    {
        protected $table = 'tests';

        public $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }
    };
    $model->qb = $qb;

    $svc2 = QueryService::make($model);
    expect($svc2->isReady())->toBeTrue();
});

it('type-hint of builder() matches Query\Builder contract for portability', function () {
    $qb = Mockery::mock(QueryBuilderContract::class);

    $model = new class extends Model
    {
        protected $table = 'tests';

        public $qb = null;

        public function newQuery()
        {
            return $this->qb;
        }
    };
    $model->qb = $qb;

    $svc = QueryService::make($model);

    // El retorno es del contrato de Query\Builder (puede ser también Eloquent\Builder)
    $builder = $svc->builder();
    expect($builder)->toBeInstanceOf(QueryBuilderContract::class);
});
