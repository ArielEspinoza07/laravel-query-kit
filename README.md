# Laravel Query Kit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/arielespinoza07/laravel-query-kit.svg?style=flat-square)](https://packagist.org/packages/arielespinoza07/laravel-query-kit)
[![Tests](https://img.shields.io/github/actions/workflow/status/arielespinoza07/laravel-query-kit/run-tests.yml?label=tests)](https://github.com/arielespinoza07/laravel-query-kit/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/arielespinoza07/laravel-query-kit.svg?style=flat-square)](https://packagist.org/packages/arielespinoza07/laravel-query-kit)

> **Requires [PHP 8.2+](https://php.net/releases/)**

---

**Laravel Query Kit** is a powerful toolkit for handling queries via criteria pattern. It's built with SOLID principles and easy to extend.

---

## ✨ Features


---

## 📁 Directory Structure

```
├── config/
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

## 🧱 Requirements

- PHP ^8.2
- Laravel ^11.0

---

## 📦 Installation

```bash
composer require arielespinoza07/laravel-query-kit
```

---

## 🚀 Usage

Code example

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
        new SortCriteria(new User, 'created_at', 'desc')->withDefaultSorts()
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
$response = $query->toJsonResource(USerResource::class);
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

## 🧪 Testing

```bash
composer test
```

---

## 🤝 Contributing

See [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## 📜 License

[MIT License](LICENSE)