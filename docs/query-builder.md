# QueryBuilder API

## Methods

- `withCriteria(...$criteria)`
- `setCriteria(array $criteria)`
- `addCriteria(CriteriaInterface $criterion)`
- `withPagination(int $page, int $perPage)`
- `builder(): Builder`
- `toModel(): ?Model`
- `toCollection(): Collection`
- `toPaginated(): LengthAwarePaginator`
- `toJsonResource(string $resourceClass): JsonResource`
- `toResourceCollection(string $resourceCollectionClass): ResourceCollection`

## Example
```php
$users = QueryBuilder::for(new User)
    ->withCriteria(
        new WhereFieldCriteria('active', '=', 1),
        new SortCriteria('created_at', 'desc')
    )
    ->withPagination(1, 15)
    ->toResourceCollection(UserCollection::class);
```
