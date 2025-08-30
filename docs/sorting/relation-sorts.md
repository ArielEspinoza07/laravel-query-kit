# Relation Sorts

Dedicated criteria exist for relationship-based sorting:

- `BelongsToOrderByCriteria`
- `BelongsToManyOrderByCriteria`
- `HasOneOrderByCriteria`
- `HasOneOrManyOrderByCriteria`
- `HasManyOrderByCriteria`
- `HasManyThroughOrderByCriteria`

Each implements `SortCriteriaInterface` and applies ordering across the join.
