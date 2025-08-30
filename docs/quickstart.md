# Quickstart

Example usage with a `User` model and criteria.

```php
use App\Models\User;
use LaravelQueryKit\Criteria\WhereFieldCriteria;
use LaravelQueryKit\Criteria\SortCriteria;
use LaravelQueryKit\Support\Facades\QueryKitBuilder;

$query = QueryKitBuilder::for(new User)
    ->withCriteria(
        new WhereFieldCriteria('email', 'like', '%john.doe%'),
        new SortCriteria('created_at', 'desc')->withDefaultSorts()
    );
```

- **Builder**
```php
$builder = $query->builder();
```

- **Single model**
```php
$model = $query->toModel();
```

- **Collection**
```php
$users = $query->toCollection();
```

- **Paginated**
```php
$users = $query->withPagination(1, 10)->toPaginated();
```

- **Resources**
```php
$json = $query->toJsonResource(UserResource::class);
$list = $query->withPagination(1, 10)->toResourceCollection(UserCollection::class);
```
