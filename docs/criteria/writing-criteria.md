# Writing Custom Criteria

To create your own criteria:

```php
use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class ActiveUsersCriteria implements CriteriaInterface
{
    public function apply(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
```

## Artisan Generator
```bash
php artisan make:criteria ActiveUsers
```

This generates a stubbed class implementing `CriteriaInterface`.
