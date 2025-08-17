# Laravel Query Kit

[![Latest on Packagist](https://img.shields.io/packagist/v/arielespinoza07/laravel-query-kit.svg?style=flat-square)](https://packagist.org/packages/arielespinoza07/laravel-query-kit)
[![Tests](https://img.shields.io/github/actions/workflow/status/arielespinoza07/laravel-query-kit/run-tests.yml?label=tests)](https://github.com/arielespinoza07/laravel-query-kit/actions)
[![Downloads](https://img.shields.io/packagist/dt/arielespinoza07/laravel-query-kit.svg?style=flat-square)](https://packagist.org/packages/arielespinoza07/laravel-query-kit)


**Laravel Query Kit** is a powerful criteria-based query builder toolkit. It's built with SOLID principles and easy to extend.

---

## 🧱 Requirements

- PHP ^8.2
- Laravel ^11.0|^12.0

---

## 📦 Installation

```bash
composer require arielespinoza07/laravel-query-kit
```

---

## ✨ Features

- Typed criteria: filters, search, pagination, sorting, soft deletes, and dates
- Filter groups with operators (`=`, `like`, `between`, `in`, `not in`, etc.) and `AND`/`OR` logic
- Sorts for relationships (`belongsTo`, `hasOne`, `hasMany`, etc.), with dedicated handlers
- Central Facade/Service for composing and executing (builder, collection, pagination, resources)
- Input pre-validation (avoids invalid queries before touching the database)
- Extensible architecture through interfaces (add your own filters and sorts)

---

## 📁 Directory Structure

```
├── src/
|   ├── Console/
|   |   └── Commands/
|   |       └── stubs/
|   ├── Contracts/
|   ├── Criteria/
|   |   └── Sort/
|   ├── Exceptions/
|   ├── Handlers/
|   ├── Providers/
|   ├── Service/
|   ├── Support/
|   |   └── Facades/
|   └── ValueObjects/
└── tests/
```

---

## 🚀 Quickstart

```php
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use LaravelQueryKit\Criteria\WhereFieldCriteria;
use LaravelQueryKit\Criteria\SortCriteria;
use LaravelQueryKit\Support\Facades\QueryBuilder;

$query = QueryBuilder::for(new User)
    ->withCriteria(
        new WhereFieldCriteria('email', 'like', '%john.doe%'),
        new SortCriteria('created_at', 'desc')->withDefaultSorts()
    );
```

1. Get the builder

```php
/** @var \Illuminate\Contracts\Database\Query\Builder $builder */
$builder = $query->builder();
```

2. Get the model

```php
/** @var \Illuminate\Database\Eloquent\Model|null $response */
$response = $query->toModel();
```

3. Execute and get the response as a collection

```php
/** @var \Illuminate\Support\Collection $response */
$response = $query->toCollection();
```

4. Execute and get the response paginated

```php
/** @var \Illuminate\Pagination\LengthAwarePaginator $response */
$response = $query->withPagination(page: 1, perPage: 10)
    ->toPaginated();
```

5. Execute and get the response as a resource (single model)

```php
/** @var \Illuminate\Http\Resources\JsonResource $response */
$response = $query->toJsonResource(UserResource::class);
```

6. Execute and get the response as a resource (collection)

```php
/** @var \Illuminate\Http\Resources\Json\ResourceCollection $response */
$response = $query->toResourceCollection(UserCollection::class);
```

7. Execute and get the response as a resource (collection paginated)

```php

/** @var \Illuminate\Http\Resources\Json\ResourceCollection $response */
$response = $query->withPagination(page: 1, perPage: 10)
    ->toResourceCollection(UserCollection::class);
```

---

## Artisan Generators

```bash
php artisan make:criteria Billing/OrderTotal
```

```php
<?php

declare(strict_types=1);

namespace App\Criteria\Billing;

use Illuminate\Contracts\Database\Query\Builder;
use LaravelQueryKit\Contracts\CriteriaInterface;

final readonly class OrderTotalCriteria implements CriteriaInterface
{
    public function __construct() {}

    public function apply(Builder $builder): Builder
    {
        //
    }
}
```

---

## 🧪 Testing

```bash
composer test
```

---

## 🤝 Contributing

See [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## Changelog

See [CHANGELOG](CHANGELOG.md) for details.

---

## Security

Report vulnerabilities by email or private issues.

---

## 📜 License

[MIT License](LICENSE)