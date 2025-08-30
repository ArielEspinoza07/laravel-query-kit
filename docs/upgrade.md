# Upgrade Guide

## vX → vY

### SortCriteria change
- Old: `new SortCriteria(new User, 'created_at', 'desc')`
- New: `new SortCriteria('created_at', 'desc')->withDefaultSorts()`

If your Criteria requires a model, implement `ModelAwareCriteriaInterface`.

---

Check CHANGELOG.md for more details.
