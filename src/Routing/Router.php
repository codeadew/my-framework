<?php

declare(strict_types=1);

namespace Dew\MyFramework\Routing;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Dew\MyFramework\Http\Middleware\Pipeline;
use Dew\MyFramework\Core\Container;

/**
 * Router
 * 
 * Handles routing with middleware support
 */
class Router
{
    /**
     * Collection of registered routes
     */
    private array $routes = [];

    /**
     * Current route group attributes
     */
    private array $groupStack = [];

    /**
     * Global middleware
     */
    private array $globalMiddleware = [];

    /**
     * Middleware aliases
     */
    private array $middlewareAliases = [];

    /**
     * The container instance
     */
    private ?Container $container = null;

    /**
     * Set the container instance
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * Register global middleware
     */
    public function addGlobalMiddleware(string $middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    /**
     * Register middleware alias
     */
    public function aliasMiddleware(string $alias, string $middleware): void
    {
        $this->middlewareAliases[$alias] = $middleware;
    }

    /**
     * Register a GET route
     */
    public function get(string $uri, mixed $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route
     */
    public function post(string $uri, mixed $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route
     */
    public function put(string $uri, mixed $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a DELETE route
     */
    public function delete(string $uri, mixed $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register a PATCH route
     */
    public function patch(string $uri, mixed $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Register a route that responds to any HTTP method
     */
    public function any(string $uri, mixed $action): Route
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        $route = null;

        foreach ($methods as $method) {
            $route = $this->addRoute($method, $uri, $action);
        }

        return $route;
    }

    /**
     * Register a route that responds to multiple HTTP methods
     */
    public function match(array $methods, string $uri, mixed $action): Route
    {
        $route = null;

        foreach ($methods as $method) {
            $route = $this->addRoute($method, $uri, $action);
        }

        return $route;
    }

    /**
     * Add a route to the collection
     */
    private function addRoute(string $method, string $uri, mixed $action): Route
    {
        $uri = $this->applyGroupPrefix($uri);
        
        $route = new Route($method, $uri, $action);

        // Apply group middleware
        if (!empty($this->groupStack)) {
            $lastGroup = end($this->groupStack);
            if (isset($lastGroup['middleware'])) {
                $route->middleware($lastGroup['middleware']);
            }
        }

        $this->routes[] = $route;

        return $route;
    }

    /**
     * Create a route group with shared attributes
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        
        call_user_func($callback, $this);
        
        array_pop($this->groupStack);
    }

    /**
     * Apply group prefix to URI
     */
    private function applyGroupPrefix(string $uri): string
    {
        if (empty($this->groupStack)) {
            return $uri;
        }

        $prefix = '';
        
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
        }

        return rtrim($prefix . '/' . ltrim($uri, '/'), '/') ?: '/';
    }

    /**
     * Dispatch the request to matching route
     */
    public function dispatch(Request $request): Response
    {
        $uri = $request->uri();
        $method = $request->method();

        // Find matching route
        $matchedRoute = null;
        $parameters = [];

        foreach ($this->routes as $route) {
            if ($route->matches($uri, $method)) {
                $matchedRoute = $route;
                $parameters = $route->extractParameters($uri);
                break;
            }
        }

        // No route found
        if ($matchedRoute === null) {
            return Response::notFound('Route not found: ' . $uri);
        }

        // Gather middleware
        $middleware = array_merge(
            $this->globalMiddleware,
            $this->resolveMiddleware($matchedRoute->getMiddleware())
        );

        // Execute through middleware pipeline
        return $this->runMiddleware($request, $middleware, function ($request) use ($matchedRoute, $parameters) {
            return $this->executeRoute($matchedRoute, $parameters, $request);
        });
    }

    /**
     * Resolve middleware aliases
     */
    protected function resolveMiddleware(array $middleware): array
    {
        return array_map(function ($name) {
            return $this->middlewareAliases[$name] ?? $name;
        }, $middleware);
    }

    /**
     * Run the middleware pipeline
     */
    protected function runMiddleware(Request $request, array $middleware, \Closure $then): Response
    {
        if ($this->container === null) {
            throw new \RuntimeException('Container not set on router');
        }

        $pipeline = new Pipeline($this->container);

        return $pipeline
            ->send($request)
            ->through($middleware)
            ->then($then);
    }

    /**
     * Execute the matched route
     */
    private function executeRoute(Route $route, array $parameters, Request $request): Response
    {
        $action = $route->getAction();

        if ($action instanceof \Closure) {
            $result = call_user_func_array($action, array_merge([$request], array_values($parameters)));
            
            if (!$result instanceof Response) {
                if (is_array($result)) {
                    return Response::json($result);
                }
                return Response::html((string) $result);
            }
            
            return $result;
        }

        if (is_string($action)) {
            return Response::html('Controller dispatching not yet implemented');
        }

        return Response::serverError('Invalid route action');
    }

    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Find route by name
     */
    public function getRouteByName(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Generate URL for named route
     */
    public function url(string $name, array $parameters = []): string
    {
        $route = $this->getRouteByName($name);

        if (!$route) {
            throw new \RuntimeException("Route not found: $name");
        }

        $uri = $route->getUri();

        foreach ($parameters as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
            $uri = str_replace('{' . $key . '?}', $value, $uri);
        }

        return $uri;
    }
}