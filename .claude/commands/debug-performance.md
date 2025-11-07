Ask the user which area to debug: frontend, backend, or database.

Then analyze and fix performance issues:

**For Frontend:**
- Check JavaScript bundle size and loading time
- Find render-blocking resources
- Check image optimization and lazy loading
- Analyze CSS efficiency
- Measure page load metrics

**For Backend:**
- Profile PHP execution time
- Find slow API endpoints
- Check database query efficiency
- Review caching (Redis, file cache)
- Check memory usage

**For Database:**
- Run EXPLAIN on queries to find issues
- Check for missing indexes
- Find N+1 query patterns
- Review JOIN efficiency
- Check table structure

Provide:
1. Current performance metrics
2. Specific bottlenecks found
3. Concrete optimizations to apply
4. Expected improvement
5. Priority order for fixes

Implement the most critical fixes automatically.
