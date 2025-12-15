<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;

/**
 * Container
 * 
 * Dependency Injection Container for automatic dependency resolution
 */
class Container
{
    /**
     * Container bindings
     */
    private array $bindings = [];

    /**
     * Singleton instances
     */
    private array $instances = [];

    /**
     * Aliases for bindings
     */
    private array $aliases = [];

    /**
     * The current globally available container instance
     */
    private static ?self $instance = null;

    /**
     * Set the globally available instance of the container
     */
    public static function setInstance(?self $container = null): ?self
    {
        return static::$instance = $container;
    }

    /**
     * Get the globally available instance of the container
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Bind a type to the container
     * 
     * @param string $abstract The abstract type (interface or class name)
     * @param Closure|string|null $concrete The concrete implementation
     * @param bool $shared Whether to treat as singleton
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        // If no concrete is provided, use the abstract as concrete
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    /**
     * Bind a singleton to the container
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as shared in the container
     */
    public function instance(string $abstract, object $instance): object
    {
        $this->instances[$abstract] = $instance;
        return $instance;
    }

    /**
     * Alias a type to a different name
     */
    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Determine if the given abstract type has been bound
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || 
               isset($this->instances[$abstract]) || 
               isset($this->aliases[$abstract]);
    }

    /**
     * Determine if the given abstract type has been resolved
     */
    public function resolved(string $abstract): bool
    {
        return isset($this->instances[$abstract]);
    }

    /**
     * Resolve the given type from the container
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        // Resolve alias
        $abstract = $this->getAlias($abstract);

        // Return singleton instance if exists
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get the concrete implementation
        $concrete = $this->getConcrete($abstract);

        // Build the object
        $object = $this->build($concrete, $parameters);

        // Store singleton if needed
        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Get the alias for an abstract if available
     */
    protected function getAlias(string $abstract): string
    {
        return $this->aliases[$abstract] ?? $abstract;
    }

    /**
     * Get the concrete type for a given abstract
     */
    protected function getConcrete(string $abstract): mixed
    {
        // If we have a binding, return it
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        // Otherwise, return the abstract itself
        return $abstract;
    }

    /**
     * Instantiate a concrete instance of the given type
     */
    protected function build(Closure|string $concrete, array $parameters = []): mixed
    {
        // If concrete is a Closure, call it
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new \RuntimeException("Target class [$concrete] does not exist.", 0, $e);
        }

        // If the type is not instantiable, throw an exception
        if (!$reflector->isInstantiable()) {
            throw new \RuntimeException("Target [$concrete] is not instantiable.");
        }

        // Get the constructor
        $constructor = $reflector->getConstructor();

        // If there's no constructor, just create instance
        if ($constructor === null) {
            return new $concrete();
        }

        // Get constructor parameters
        $dependencies = $constructor->getParameters();

        // Resolve all dependencies
        $resolvedDependencies = $this->resolveDependencies($dependencies, $parameters);

        // Create and return instance with resolved dependencies
        return $reflector->newInstanceArgs($resolvedDependencies);
    }

    /**
     * Resolve all dependencies for a set of parameters
     */
    protected function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // If parameter was explicitly provided, use it
            if (array_key_exists($dependency->getName(), $parameters)) {
                $results[] = $parameters[$dependency->getName()];
                continue;
            }

            // Get the type hint
            $type = $dependency->getType();

            // If no type hint, try to use default value
            if ($type === null) {
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                    continue;
                }

                throw new \RuntimeException(
                    "Cannot resolve dependency [{$dependency->getName()}] - no type hint and no default value."
                );
            }

            // If type is not a class (built-in type like string, int, etc.)
            if ($type->isBuiltin()) {
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                    continue;
                }

                throw new \RuntimeException(
                    "Cannot resolve built-in dependency [{$dependency->getName()}] of type [{$type->getName()}]."
                );
            }

            // Resolve class dependency
            $results[] = $this->make($type->getName());
        }

        return $results;
    }

    /**
     * Call a method and inject its dependencies
     */
    public function call(callable|array $callback, array $parameters = []): mixed
    {
        // Handle array format [class, method]
        if (is_array($callback)) {
            [$class, $method] = $callback;
            
            // Resolve class instance if it's a string
            if (is_string($class)) {
                $class = $this->make($class);
            }

            $reflector = new \ReflectionMethod($class, $method);
            $dependencies = $reflector->getParameters();
            $resolvedDependencies = $this->resolveDependencies($dependencies, $parameters);

            return $reflector->invokeArgs($class, $resolvedDependencies);
        }

        // Handle Closure
        if ($callback instanceof Closure) {
            $reflector = new \ReflectionFunction($callback);
            $dependencies = $reflector->getParameters();
            $resolvedDependencies = $this->resolveDependencies($dependencies, $parameters);

            return $reflector->invokeArgs($resolvedDependencies);
        }

        return call_user_func($callback);
    }

    /**
     * Flush the container of all bindings and instances
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
    }
}