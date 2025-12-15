<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Dew\MyFramework\Core\Container;
use Closure;

/**
 * Pipeline
 * 
 * Executes a stack of middleware in sequence
 */
class Pipeline
{
    /**
     * The container instance
     */
    protected Container $container;

    /**
     * The object being passed through the pipeline
     */
    protected Request $passable;

    /**
     * The array of middleware
     */
    protected array $pipes = [];

    /**
     * The method to call on each pipe
     */
    protected string $method = 'handle';

    /**
     * Create a new Pipeline instance
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set the object being sent through the pipeline
     */
    public function send(Request $passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * Set the array of pipes
     */
    public function through(array $pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    /**
     * Set the method to call on the pipes
     */
    public function via(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Run the pipeline with a final destination callback
     */
    public function then(Closure $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    /**
     * Get the final piece of the Closure onion
     */
    protected function prepareDestination(Closure $destination): Closure
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion
     */
    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                // If pipe is a string, resolve it from container
                if (is_string($pipe)) {
                    $pipe = $this->container->make($pipe);
                }

                // Call the middleware
                return $pipe->{$this->method}($passable, $stack);
            };
        };
    }
}