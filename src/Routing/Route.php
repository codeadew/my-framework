<?php

declare(strict_types=1);

namespace Dew\MyFramework\Routing;

/**
 * Route
 * 
 * Represents a single route definition with advanced parameter handling
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
     * Parameter constraints (regex patterns)
     */
    private array $parameterConstraints = [];

    /**
     * Optional parameters
     */
    private array $optionalParameters = [];

    /**
     * Default parameter values
     */
    private array $defaults = [];

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
     * Add constraint to route parameter
     * 
     * Examples:
     * ->where('id', '[0-9]+')
     * ->where(['id' => '[0-9]+', 'slug' => '[a-z\-]+'])
     */
    public function where(string|array $name, ?string $pattern = null): self
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->parameterConstraints[$key] = $value;
            }
        } else {
            $this->parameterConstraints[$name] = $pattern;
        }

        // Recompile route with new constraints
        $this->compileRoute();

        return $this;
    }

    /**
     * Constrain parameter to numbers only
     */
    public function whereNumber(string $name): self
    {
        return $this->where($name, '[0-9]+');
    }

    /**
     * Constrain parameter to alphabetic characters only
     */
    public function whereAlpha(string $name): self
    {
        return $this->where($name, '[a-zA-Z]+');
    }

    /**
     * Constrain parameter to alphanumeric characters only
     */
    public function whereAlphaNumeric(string $name): self
    {
        return $this->where($name, '[a-zA-Z0-9]+');
    }

    /**
     * Constrain parameter to valid slug format
     */
    public function whereSlug(string $name): self
    {
        return $this->where($name, '[a-z0-9\-]+');
    }

    /**
     * Constrain parameter to UUID format
     */
    public function whereUuid(string $name): self
    {
        return $this->where($name, '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
    }

    /**
     * Set default value for optional parameter
     */
    public function defaults(array $defaults): self
    {
        $this->defaults = array_merge($this->defaults, $defaults);
        return $this;
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
            $value = $matches[$index + 1] ?? null;
            
            // Use default value if parameter is empty and optional
            if (($value === '' || $value === null) && isset($this->defaults[$name])) {
                $value = $this->defaults[$name];
            }
            
            // Skip empty optional parameters without defaults
            if ($value === '' && in_array($name, $this->optionalParameters)) {
                continue;
            }

            $parameters[$name] = $value;
        }

        return $parameters;
    }

    /**
     * Compile route pattern into regex
     */
    private function compileRoute(): void
    {
        // Extract parameter names and whether they're optional
        // {id} = required, {id?} = optional
        preg_match_all('/{([a-zA-Z_][a-zA-Z0-9_]*)(\?)?}/', $this->uri, $matches, PREG_SET_ORDER);
        
        $this->parameterNames = [];
        $this->optionalParameters = [];
        
        foreach ($matches as $match) {
            $name = $match[1];
            $optional = isset($match[2]) && $match[2] === '?';
            
            $this->parameterNames[] = $name;
            
            if ($optional) {
                $this->optionalParameters[] = $name;
            }
        }

        // Build regex pattern
        $pattern = $this->uri;
        
        foreach ($this->parameterNames as $name) {
            $isOptional = in_array($name, $this->optionalParameters);
            
            // Get constraint pattern for this parameter (if any)
            $constraint = $this->parameterConstraints[$name] ?? '[^/]+';
            
            // Replace parameter placeholder with regex
            if ($isOptional) {
                // Optional parameter: can be empty
                $pattern = str_replace('{' . $name . '?}', '(' . $constraint . ')?', $pattern);
            } else {
                // Required parameter: must have value
                $pattern = str_replace('{' . $name . '}', '(' . $constraint . ')', $pattern);
            }
        }
        
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

    /**
     * Get optional parameters
     */
    public function getOptionalParameters(): array
    {
        return $this->optionalParameters;
    }

    /**
     * Get parameter constraints
     */
    public function getConstraints(): array
    {
        return $this->parameterConstraints;
    }

    /**
     * Get default values
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }
}