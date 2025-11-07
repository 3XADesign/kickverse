Ask the user which resource to create an API for.

Then generate a complete RESTful API endpoint with:

**API Controller** (app/controllers/api/[Resource]Controller.php):
- index() - GET /api/[resource] - List all
- show($id) - GET /api/[resource]/:id - Get one
- create() - POST /api/[resource] - Create new
- update($id) - PUT /api/[resource]/:id - Update
- delete($id) - DELETE /api/[resource]/:id - Delete

**Model** (app/models/[Resource].php):
- CRUD methods with prepared statements
- Validation rules
- Database queries
- Relationships

**Features included:**
- Input validation and sanitization
- SQL injection prevention
- Proper HTTP status codes (200, 201, 400, 404, 500)
- JSON responses
- Error handling
- Authentication checks
- Rate limiting consideration

**Route registration:**
Add routes to routes/web.php

Provide example API calls with curl commands.
