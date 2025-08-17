<?php

declare(strict_types=1);

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

it('stub has strict types, placeholders and expected class skeleton', function () {
    $cwd = getcwd();
    $directoryPath = '/src/Console/Commands/stubs/';
    $stubFile = 'criteria.stub';
    $path = $cwd.$directoryPath.$stubFile;

    expect(file_exists($path))->toBeTrue();

    $stub = file_get_contents($path);

    expect($stub)->toContain('declare(strict_types=1);')
        ->and($stub)->toContain('{{ namespace }}')
        ->and($stub)->toContain('{{ class }}')
        ->and($stub)->toContain('use Illuminate\Contracts\Database\Query\Builder;')
        ->and($stub)->toContain('use LaravelQueryKit\Contracts\CriteriaInterface;')
        ->and($stub)->toContain('final readonly class {{ class }} implements CriteriaInterface')
        ->and($stub)->toContain('public function apply(Builder $builder): Builder');
});

it('stub compiles into a concrete class with correct namespace and class name', function () {
    $cwd = getcwd();
    $directoryPath = '/src/Console/Commands/stubs/';
    $stubFile = 'criteria.stub';
    $path = $cwd.$directoryPath.$stubFile;

    $stub = file_get_contents($path);

    $ns = 'App\\Criteria';
    $class = 'OrderTotalCriteria';
    $output = str_replace(
        ['{{ namespace }}', '{{ class }}'],
        [$ns, $class],
        $stub
    );

    $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'lqk_tests';
    if (is_dir($tmpDir)) {
        rmdir($tmpDir);
    }
    @mkdir($tmpDir, 0777, true);

    $tmpFile = $tmpDir.DIRECTORY_SEPARATOR."$class.php";
    file_put_contents($tmpFile, $output);

    require_once $tmpFile;

    $fqcn = $ns.'\\'.$class;

    expect(class_exists($fqcn))->toBeTrue()
        ->and(is_subclass_of($fqcn, CriteriaInterface::class))->toBeTrue();

    $ref = new ReflectionClass($fqcn);
    $m = $ref->getMethod('apply');

    $params = $m->getParameters();
    expect($params)->toHaveCount(1)
        ->and($params[0]->getType())->toBeInstanceOf(ReflectionNamedType::class)
        ->and($params[0]->getType()->getName())->toBe(Builder::class);

    $ret = $m->getReturnType();
    expect($ret)->toBeInstanceOf(ReflectionNamedType::class)
        ->and($ret->getName())->toBe(Builder::class);

    unlink($tmpFile);
    rmdir($tmpDir);
});
