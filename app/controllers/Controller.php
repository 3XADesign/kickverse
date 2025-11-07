<?php
/**
 * Base Controller Class
 */

class Controller {
    protected $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/app.php';

        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Load a view with layout
     */
    protected function view($view, $data = [], $layout = 'layouts/main') {
        extract($data);

        // Add config to data
        $config = $this->config;

        // Capture view content
        $viewPath = $this->config['views'] . '/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("View not found: {$view}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Load layout if specified
        if ($layout) {
            $layoutPath = $this->config['views'] . '/' . $layout . '.php';

            if (file_exists($layoutPath)) {
                require $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Get request data (POST or JSON)
     */
    protected function input($key = null, $default = null) {
        $input = $_POST;

        // Check for JSON input
        if (empty($input)) {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true) ?? [];
        }

        if ($key === null) {
            return $input;
        }

        return $input[$key] ?? $default;
    }

    /**
     * Redirect
     */
    protected function redirect($url, $statusCode = 302) {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Get current user from session
     */
    protected function getUser() {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate CSRF token
     */
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash($type) {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    /**
     * Validate input
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $rule);

            foreach ($rulesArray as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field][] = "{$field} is required";
                } elseif (substr($r, 0, 4) === 'min:') {
                    $min = (int) str_replace('min:', '', $r);
                    if (strlen((string)$value) < $min) {
                        $errors[$field][] = "{$field} must be at least {$min} characters";
                    }
                } elseif (substr($r, 0, 4) === 'max:') {
                    $max = (int) str_replace('max:', '', $r);
                    if (strlen((string)$value) > $max) {
                        $errors[$field][] = "{$field} must not exceed {$max} characters";
                    }
                } elseif ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field} must be a valid email";
                }
            }
        }

        return empty($errors) ? true : $errors;
    }
}
