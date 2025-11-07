Ask the user which table to optimize, or analyze the entire database if not specified.

Then perform a complete database optimization:

**1. Query Analysis:**
- Find slow queries (> 50ms execution time)
- Run EXPLAIN on each query
- Identify missing indexes
- Detect N+1 query patterns
- Check for table scans

**2. Index Optimization:**
- Analyze query patterns from code
- Suggest composite indexes
- Find unused indexes to remove
- Check index cardinality
- Recommend covering indexes

**3. Table Structure:**
- Review normalization
- Check data types (use INT instead of VARCHAR where possible)
- Find large TEXT/BLOB fields that should be separate
- Check for proper foreign keys

**4. Generate Fixes:**
Create SQL migration file with:
- New indexes to add
- Indexes to remove
- Table alterations
- Query optimizations in code

Provide before/after performance estimates.

Show specific SQL commands to run and which PHP files need query updates.
