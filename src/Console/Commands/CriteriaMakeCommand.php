<?php

declare(strict_types=1);

namespace LaravelQueryKit\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

final class CriteriaMakeCommand extends GeneratorCommand
{
    protected $signature = 'make:criteria {name : The criteria class name} {--f|force : Overwrite if the file exists}';

    protected $description = 'Create a new criteria';

    protected $type = 'Criteria';

    protected function getStub(): string
    {
        return $this->resolveStubPath('./stubs/criteria.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Criteria';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int, mixed>
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the criteria already exists'],
        ];
    }
}
