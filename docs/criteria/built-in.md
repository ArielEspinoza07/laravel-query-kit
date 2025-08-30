# Built-in Criteria

## Filters
- `WhereFieldCriteria(field, operator, value)`
- `WhereInCriteria(field, values)` / `WhereNotInCriteria`
- `WhereBetweenCriteria(field, [from, to])` / `WhereNotBetweenCriteria`

## Search
- `SearchCriteria`: global text search across multiple fields

## Soft Deletes
- `WithTrashedCriteria`
- `OnlyTrashedCriteria`

## Included Relations
- `IncludedCriteria`: eager load related models

## Sorting
- `SortCriteria(column, direction)->withDefaultSorts()`
- Relation-based sorts via dedicated Criteria (`BelongsToOrderByCriteria`, etc.)
