<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;

/**
 * Controller
 * 
 * Base controller class with common functionality
 */
abstract class Controller
{
    /**
     * The current request instance
     */
    protected Request $request;

    /**
     * The application instance
     */
    protected Application $app;

    /**
     * Create a new controller instance
     */
    public function __construct(Request $request, Application $app)
    {
        $this->request = $request;
        $this->app = $app;
    }

    /**
     * Get input from request
     */
    protected function input(string $key = null, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    /**
     * Get all input
     */
    protected function all(): array
    {
        return $this->request->all();
    }

    /**
     * Get only specified input keys
     */
    protected function only(array $keys): array
    {
        return $this->request->only($keys);
    }

    /**
     * Check if request has input
     */
    protected function has(string $key): bool
    {
        return $this->request->has($key);
    }

    /**
     * Return HTML response
     */
    protected function html(string $content, int $status = 200): Response
    {
        return Response::html($content, $status);
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * Return redirect response
     */
    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    /**
     * Return 404 response
     */
    protected function notFound(string $message = 'Not Found'): Response
    {
        return Response::notFound($message);
    }

    /**
     * Return 403 response
     */
    protected function forbidden(string $message = 'Forbidden'): Response
    {
        return Response::forbidden($message);
    }

    /**
     * Return 401 response
     */
    protected function unauthorized(string $message = 'Unauthorized'): Response
    {
        return Response::unauthorized($message);
    }

    /**
     * Render a view (placeholder for now)
     */
    protected function view(string $view, array $data = []): Response
    {
        // This will be implemented in the View step
        // For now, return a simple response
        return $this->html($this->renderSimpleView($view, $data));
    }

    /**
     * Simple view rendering (temporary)
     */
    protected function renderSimpleView(string $view, array $data): string
    {
        $viewPath = BASE_PATH . "/views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            return "<h1>View not found: {$view}</h1>";
        }

        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include view file
        include $viewPath;
        
        // Get and return contents
        return ob_get_clean();
    }

    /**
     * Validate input (simple validation for now)
     */
    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $this->input($field);
            $rulesArray = explode('|', $fieldRules);
            
            foreach ($rulesArray as $rule) {
                $error = $this->validateRule($field, $value, $rule);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }

        if (!empty($errors)) {
            throw new \RuntimeException('Validation failed: ' . json_encode($errors));
        }

        return $this->only(array_keys($rules));
    }

    /**
     * Validate a single rule
     */
    protected function validateRule(string $field, mixed $value, string $rule): ?string
    {
        // Parse rule with parameters (e.g., "min:5")
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;

        return match($ruleName) {
            'required' => empty($value) ? "{$field} is required" : null,
            'email' => !filter_var($value, FILTER_VALIDATE_EMAIL) ? "{$field} must be a valid email" : null,
            'min' => strlen($value) < (int)$parameter ? "{$field} must be at least {$parameter} characters" : null,
            'max' => strlen($value) > (int)$parameter ? "{$field} must not exceed {$parameter} characters" : null,
            'numeric' => !is_numeric($value) ? "{$field} must be numeric" : null,
            default => null,
        };
    }

    /**
     * Get a service from the container
     */
    protected function make(string $abstract): mixed
    {
        return $this->app->make($abstract);
    }

    /**
     * Flash data to session (placeholder)
     */
    protected function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get flashed data (placeholder)
     */
    protected function old(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}