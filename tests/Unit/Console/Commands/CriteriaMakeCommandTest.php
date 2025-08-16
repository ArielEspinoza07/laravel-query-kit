<?php

declare(strict_types=1);

use Illuminate\Console\GeneratorCommand;
use LaravelQueryKit\Console\Commands\CriteriaMakeCommand;
use Symfony\Component\Console\Input\InputOption;

it('is final and extends GeneratorCommand', function () {
    $ref = new ReflectionClass(CriteriaMakeCommand::class);

    expect($ref->isFinal())->toBeTrue()
        ->and($ref->isSubclassOf(GeneratorCommand::class))->toBeTrue();
});

it('has the exact signature with name and force alias', function () {
    $ref = new ReflectionClass(CriteriaMakeCommand::class);
    $defaults = $ref->getDefaultProperties(); // <-- no instancia
    $signature = $defaults['signature'];

    expect($signature)->toBe(
        'make:criteria {name : The criteria class name} {--f|force : Overwrite if the file exists}'
    );
});

it('has the expected description', function () {
    $ref = new ReflectionClass(CriteriaMakeCommand::class);
    $defaults = $ref->getDefaultProperties();

    expect($defaults['description'])->toBe('Create a new criteria');
});

it('has type set to Criteria', function () {
    $ref = new ReflectionClass(CriteriaMakeCommand::class);
    $defaults = $ref->getDefaultProperties();

    expect($defaults['type'])->toBe('Criteria');
});

it('defines getOptions() with force option (alias f, VALUE_NONE)', function () {
    $ref = new ReflectionClass(CriteriaMakeCommand::class);
    $m = $ref->getMethod('getOptions');
    $m->setAccessible(true);

    /** @var array<int, array{0:string,1:string|false,2:int,3:string}> $options */
    $options = $m->invoke($ref->newInstanceWithoutConstructor());

    $force = collect($options)->first(fn ($opt) => $opt[0] === 'force');

    expect($force)->not->toBeNull()
        ->and($force[1])->toBe('f')
        ->and($force[2])->toBe(InputOption::VALUE_NONE)
        ->and($force[3])->toBe('Create the class even if the criteria already exists');
});
