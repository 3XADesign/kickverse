# Ejemplo de Routing para Productos Admin

## Añade estas rutas a tu archivo de routing principal

```php
// ============================================
// ADMIN - PRODUCTOS
// ============================================

// Lista de productos (con paginación y filtros)
if ($uri === '/admin/productos' && $method === 'GET') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->index();
    exit;
}

// Ver detalles de un producto (AJAX para modal)
if (preg_match('#^/api/admin/productos/(\d+)$#', $uri, $matches) && $method === 'GET') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->show($matches[1]);
    exit;
}

// Crear nuevo producto (formulario)
if ($uri === '/admin/productos/crear' && $method === 'GET') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->create();
    exit;
}

// Guardar nuevo producto
if ($uri === '/admin/productos' && $method === 'POST') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->store();
    exit;
}

// Editar producto (formulario)
if (preg_match('#^/admin/productos/editar/(\d+)$#', $uri, $matches) && $method === 'GET') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->edit($matches[1]);
    exit;
}

// Actualizar producto
if (preg_match('#^/admin/productos/(\d+)$#', $uri, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->update($matches[1]);
    exit;
}

// Eliminar producto
if (preg_match('#^/admin/productos/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/../app/controllers/admin/ProductosController.php';
    $controller = new ProductosController();
    $controller->delete($matches[1]);
    exit;
}
```

## Endpoint API necesario para el modal

El modal necesita que exista el endpoint:
```
GET /api/admin/productos/{id}
```

Este endpoint devuelve JSON con todos los datos del producto:
- Información básica
- Imágenes (todas las variantes)
- Tallas y stock
- Equipo y liga
- Precios

## Nota

El controlador ya está preparado para estos endpoints. Solo necesitas agregar las rutas a tu archivo de routing principal.
