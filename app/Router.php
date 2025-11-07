<?php
/**
 * Simple Router Class
 * Handles URL routing to controllers
 */

class Router {
    private $routes = [];
    private $currentRoute = null;

    /**
     * Add GET route
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Add POST route
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add PUT route
     */
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Add DELETE route
     */
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add route to routes array
     */
    private function addRoute($method, $path, $handler) {
        // Convert route path to regex pattern
        $pattern = $this->pathToRegex($path);

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * Convert route path to regex pattern
     * Example: /api/products/:id => /^\/api\/products\/([^\/]+)$/
     */
    private function pathToRegex($path) {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);

        // Replace :param with regex group
        $pattern = preg_replace('/:([\w]+)/', '([^\/]+)', $pattern);

        // Add start and end anchors
        $pattern = '/^' . $pattern . '$/';

        return $pattern;
    }

    /**
     * Dispatch the router
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Support for _method override (for PUT/DELETE from forms)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                // Remove the full match, keep only captured groups
                array_shift($matches);

                $this->currentRoute = $route;
                return $this->callHandler($route['handler'], $matches);
            }
        }

        // No route matched - 404
        $this->notFound();
    }

    /**
     * Call route handler
     */
    private function callHandler($handler, $params = []) {
        if (is_callable($handler)) {
            // Handler is a closure
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            // Handler is "ControllerName@methodName"
            list($controllerName, $methodName) = explode('@', $handler);

            // Determine controller path based on route
            if (strpos($this->currentRoute['path'], '/api/admin/') === 0) {
                $controllerPath = __DIR__ . '/controllers/admin/' . $controllerName . '.php';
            } elseif (strpos($this->currentRoute['path'], '/api/') === 0) {
                $controllerPath = __DIR__ . '/controllers/api/' . $controllerName . '.php';
            } elseif (strpos($this->currentRoute['path'], '/admin/') === 0) {
                $controllerPath = __DIR__ . '/controllers/admin/' . $controllerName . '.php';
            } else {
                $controllerPath = __DIR__ . '/controllers/' . $controllerName . '.php';
            }

            if (!file_exists($controllerPath)) {
                die("Controller not found: {$controllerPath}");
            }

            require_once $controllerPath;

            if (!class_exists($controllerName)) {
                die("Controller class not found: {$controllerName}");
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $methodName)) {
                die("Method not found: {$controllerName}@{$methodName}");
            }

            return call_user_func_array([$controller, $methodName], $params);
        }

        die('Invalid route handler');
    }

    /**
     * 404 Not Found
     */
    private function notFound() {
        http_response_code(404);

        // Check if it's an API request
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Endpoint no encontrado'
            ]);
        } else {
            // Include custom 404 page
            $notFoundPage = __DIR__ . '/../public/404.php';
            if (file_exists($notFoundPage)) {
                include $notFoundPage;
            } else {
                // Fallback if 404.php doesn't exist
                echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Kickverse</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .container {
            text-align: center;
            padding: 2rem;
        }
        h1 {
            font-size: 6rem;
            margin: 0;
        }
        p {
            font-size: 1.5rem;
            margin: 1rem 0;
        }
        a {
            color: white;
            text-decoration: none;
            border: 2px solid white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        a:hover {
            background: white;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Página no encontrada</p>
        <a href="/">Volver al inicio</a>
    </div>
</body>
</html>';
            }
        }

        exit;
    }

    /**
     * Get all registered routes
     */
    public function getRoutes() {
        return $this->routes;
    }
}
