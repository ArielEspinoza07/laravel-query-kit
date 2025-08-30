# Model-Aware Criteria

Some criteria require access to the Eloquent `Model` instance, for example when 
sorting by relationships or resolving default columns.

## Interface
```php
interface ModelAwareCriteriaInterface
{
    public function withModel(Model $model): self;
}
```

- **Usage**: QueryService automatically detects criteria implementing this interface and calls `withModel($model)`.
- **Example**:
```php
final class ModelAwareExampleCriteria implements ModelAwareCriteriaInterface
{
    public function __construct(private ?Model model) {}
    
    public function apply(Builder $builder): Builder
    {
        // TODO: Implement apply() method.
        return $builder;
    }
    
    public function withModel(Model $model): self
    {
        return new self(model: $model);
    }
}
```
