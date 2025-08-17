<?php

declare(strict_types=1);

namespace LaravelQueryKit\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelQueryKit\Console\Commands\CriteriaMakeCommand;
use LaravelQueryKit\QueryBuilder;

final class QueryKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('query-kit', fn () => new QueryBuilder);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                CriteriaMakeCommand::class,
            );
        }
    }
}
