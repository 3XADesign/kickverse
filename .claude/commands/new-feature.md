Ask the user what feature they want to create, then generate a complete feature implementation with:

1. Database migration (SQL file)
2. PHP Model with CRUD methods
3. PHP Controller with validation
4. API endpoint (RESTful)
5. Frontend view with JavaScript
6. CSS styling
7. Documentation

Create files following this structure:
- app/models/[Feature].php
- app/controllers/[Feature]Controller.php
- app/controllers/api/[Feature]Controller.php
- app/views/[feature]/index.php
- migrations/YYYY_MM_DD_create_[feature]_table.sql
- public/js/[feature].js
- public/css/[feature].css

Always include:
- Input validation
- SQL injection prevention
- Error handling
- Responsive design
- Proper indexes on database tables
