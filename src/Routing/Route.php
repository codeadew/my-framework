<?php

declare(strict_types=1);

namespace Dew\MyFramework\Routing;

/**
 * Route
 * 
 * Represents a single route definition
 */
class Route
{
    /**
     * HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    private string $method;

    /**
     * URI pattern (e.g., /users/{id})
     */
    private string $uri;

    /**
     * Action to execute (closure or controller@method)
     */
    private mixed $action;

    /**
     * Route name (optional)
     */
    private ?string $name = null;

    /**
     * Middleware to apply to this route
     */
    private array $middleware = [];

    /**
     * Regular expression pattern for matching
     */
    private ?string $regex = null;

    /**
     * Parameter names extracted from URI
     */
    private array $parameterNames = [];

    /**
     * Create a new Route instance
     */
    public function __construct(string $method, string $uri, mixed $action)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->action = $action;
        
        $this->compileRoute();
    }

    /**
     * Get HTTP method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get URI pattern
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get action
     */
    public function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * Set route name
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get route name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Add middleware to route
     */
    public function middleware(string|array $middleware): self
    {
        $middleware = is_array($middleware) ? $middleware : [$middleware];
        $this->middleware = array_merge($this->middleware, $middleware);
        return $this;
    }

    /**
     * Get middleware
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Check if route matches the given URI and method
     */
    public function matches(string $uri, string $method): bool
    {
        if ($this->method !== strtoupper($method)) {
            return false;
        }

        return (bool) preg_match($this->regex, $uri);
    }

    /**
     * Extract parameters from URI
     */
    public function extractParameters(string $uri): array
    {
        if (!preg_match($this->regex, $uri, $matches)) {
            return [];
        }

        $parameters = [];
        
        foreach ($this->parameterNames as $index => $name) {
            $parameters[$name] = $matches[$index + 1] ?? null;
        }

        return $parameters;
    }

    /**
     * Compile route pattern into regex
     */
    private function compileRoute(): void
    {
        // Extract parameter names from URI pattern
        // e.g., /users/{id}/posts/{postId} -> ['id', 'postId']
        preg_match_all('/{([a-zA-Z_][a-zA-Z0-9_]*)}/', $this->uri, $matches);
        $this->parameterNames = $matches[1];

        // Convert URI pattern to regex
        // {id} becomes ([^/]+), {id?} becomes ([^/]*), etc.
        $pattern = preg_replace('/{([a-zA-Z_][a-zA-Z0-9_]*)\?}/', '([^/]*)', $this->uri);
        $pattern = preg_replace('/{([a-zA-Z_][a-zA-Z0-9_]*)}/', '([^/]+)', $pattern);
        
        // Escape forward slashes and add anchors
        $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
        
        $this->regex = $pattern;
    }

    /**
     * Get compiled regex pattern
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * Get parameter names
     */
    public function getParameterNames(): array
    {
        return $this->parameterNames;
    }
}