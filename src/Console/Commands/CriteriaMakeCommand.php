<?php

declare(strict_types=1);

namespace LaravelQueryKit\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

final class CriteriaMakeCommand extends GeneratorCommand
{
    protected $signature = 'make:criteria {name : The criteria class name} {--f|force : Overwrite if the file exists} {--s|sort : Create sort criteria}';

    protected $description = 'Create a new criteria class';

    protected $type = 'Criteria';

    protected function getStub(): string
    {
        if ($this->option('sort')) {
            return $this->resolveStubPath('/stubs/criteria-sort.stub');
        }

        return $this->resolveStubPath('/stubs/criteria.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        $custom = $this->laravel->basePath(trim($stub, '/'));

        return file_exists($custom)
            ? $custom
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
            ['sort', 's', InputOption::VALUE_OPTIONAL, 'Create criteria for sorting by a relationship'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function qualifyClass($name)
    {
        $qualified = parent::qualifyClass($name);

        if (! str_ends_with($qualified, 'Criteria')) {
            $qualified .= 'Criteria';
        }

        return $qualified;
    }
}
