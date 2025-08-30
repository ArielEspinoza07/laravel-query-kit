# FAQ

**Q: Why does SortCriteria not take a Model by default?**  
A: To simplify usage. When needed, use `ModelAwareCriteriaInterface`.

**Q: What's the difference between `builder()` and `toCollection()`?**  
A: `builder()` returns the underlying query builder. `toCollection()` executes the query and returns results.

**Q: Can I use Query Kit with custom resources?**  
A: Yes, use `toJsonResource()` or `toResourceCollection()` and pass your resource class.
