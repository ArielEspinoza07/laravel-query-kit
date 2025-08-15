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
|   ├── Commands/
|   |   └── stubs/
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

## ⚙️ Configuration


---

## 🚀 Usage

1. Code example

```php
use App\Models\User;
use LaravelQueryKit\Criteria\WhereFieldCriteria;
use LaravelQueryKit\Criteria\SortCriteria;
use LaravelQueryKit\QueryBuilder;

$query = QueryBuilder::for(new User)
    ->withCriteria(
        new WhereFieldCriteria('email', 'like', '%john.doe%'),
        new SortCriteria(new User, 'created_at', 'desc')->withDefaultSorts()
    );

/** @var \Illuminate\Contracts\Database\Query\Builder $builder */
$builder = $query->builder();

/** @var \Illuminate\Database\Eloquent\Model|null $response */
$response = $query->toModel();

/** @var \Illuminate\Support\Collection $response */
$response = $query->toCollection();

/** @var \Illuminate\Pagination\LengthAwarePaginator $response */
$response = $query->toPaginated();

/** @var \Illuminate\Http\Resources\JsonResource $response */
$response = $query->toJsonResource(USerResource::class);

/** @var \Illuminate\Http\Resources\Json\ResourceCollection $response */
$response = $query->toResourceCollection();

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