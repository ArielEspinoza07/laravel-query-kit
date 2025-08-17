<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use LaravelQueryKit\Contracts\CriteriaInterface;

it('artisan make:criteria generates the file from the stub and compiles correctly', function () {
    $fs = new Filesystem;

    expect(array_key_exists('make:criteria', Artisan::all()))
        ->toBeTrue('Command make:criteria is not registered');

    $name = 'Billing/OrderTotal';
    $exit = Artisan::call('make:criteria', [
        'name' => $name,
        '--force' => true,
    ]);

    expect($exit)->toBe(0);

    $expectedPath = app_path('Criteria/Billing/OrderTotalCriteria.php');
    expect(file_exists($expectedPath))
        ->toBeTrue("File not generated on: $expectedPath");

    $code = file_get_contents($expectedPath);

    $ns = null;
    $cls = null;
    if (preg_match('/^namespace\s+([^;]+);/m', $code, $m)) {
        $ns = trim($m[1]);
    }
    if (preg_match('/^\s*(?:(?:final\s+|abstract\s+)?readonly\s+|final\s+|abstract\s+)?class\s+([A-Za-z_]\w*)\b/m', $code, $m)) {
        $cls = $m[1];
    }

    expect($ns)->not()->toBeNull('The namespace could not be detected in the generated file')
        ->and($cls)->not()->toBeNull('Could not detect class name in generated file');

    require_once $expectedPath;

    $fqcn = $ns.'\\'.$cls;

    expect(class_exists($fqcn))->toBeTrue()
        ->and(is_subclass_of($fqcn, CriteriaInterface::class))->toBeTrue();

    $fs->delete($expectedPath);

    $billingDir = app_path('Criteria/Billing');
    $criteriaDir = app_path('Criteria');

    if (is_dir($billingDir) && count(glob($billingDir.'/*')) === 0) {
        @rmdir($billingDir);
    }
    if (is_dir($criteriaDir) && count(glob($criteriaDir.'/*')) === 0) {
        @rmdir($criteriaDir);
    }
});

it('allows overwriting with --force without errors', function () {
    $fs = new Filesystem;

    $pre = app_path('Criteria/DummyCriteria.php');
    @mkdir(dirname($pre), 0777, true);
    file_put_contents($pre, "<?php\n// dummy\n");

    $exit = Artisan::call('make:criteria', [
        'name' => 'Dummy',
        '--force' => true,
    ]);

    expect($exit)->toBe(0);
    expect(file_exists($pre))->toBeTrue();

    $code = file_get_contents($pre);
    expect($code)->not->toContain('// dummy')
        ->and($code)->toContain('final readonly class DummyCriteria');

    $fs->delete($pre);
    $criteriaDir = app_path('Criteria');
    if (is_dir($criteriaDir) && count(glob($criteriaDir.'/*')) === 0) {
        @rmdir($criteriaDir);
    }
});
