<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Dew\MyFramework\Routing\Router;
use Dew\MyFramework\Core\Providers\RouteServiceProvider;

/**
 * Application
 * 
 * The core application class that manages the entire framework
 */
class Application
{
    /**
     * Framework version
     */
    private const VERSION = '1.0.0-dev';

    /**
     * Application base path
     */
    private string $basePath;

    /**
     * The container instance
     */
    private Container $container;

    /**
     * Service providers
     */
    private array $serviceProviders = [];

    /**
     * Service providers that have been booted
     */
    private bool $booted = false;

    /**
     * Debug mode
     */
    private bool $debug = true;

    /**
     * Create a new application instance
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->container = new Container();
        
        $this->registerBaseBindings();
        $this->registerCoreServices();
        $this->registerServiceProviders();
    }

    /**
     * Register base bindings into the container
     */
    protected function registerBaseBindings(): void
    {
        Container::setInstance($this->container);
        
        $this->container->instance(Container::class, $this->container);
        $this->container->instance(Application::class, $this);
        $this->container->alias(Application::class, 'app');
    }

   
        /**
     * Register core framework services
     */
    protected function registerCoreServices(): void
    {
        // Register logger as singleton
        $this->container->singleton(
            \Dew\MyFramework\Core\Services\Logger::class,
            function ($container) {
                return new \Dew\MyFramework\Core\Services\Logger(
                    $this->basePath . '/storage/logs/app.log'
                );
            }
        );

        // Register database service
        $this->container->bind(\Dew\MyFramework\Core\Services\Database::class);
        
        // Register user service
        $this->container->bind(\Dew\MyFramework\Core\Services\UserService::class);

        // Register controllers (they'll be auto-resolved when needed)
        $this->container->bind(\Dew\MyFramework\Controllers\HomeController::class);
        $this->container->bind(\Dew\MyFramework\Controllers\UserController::class);
        $this->container->bind(\Dew\MyFramework\Controllers\ApiController::class);
    }
    
    /**
     * Register service providers
     */
    protected function registerServiceProviders(): void
    {
        $this->register(new RouteServiceProvider($this));
    }

    /**
     * Register a service provider
     */
    public function register(ServiceProvider $provider): void
    {
        $provider->register();
        $this->serviceProviders[] = $provider;
    }

    /**
     * Boot all registered service providers
     */
    protected function bootProviders(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->serviceProviders as $provider) {
            $provider->boot();
        }

        $this->booted = true;
    }

    /**
     * Handle an incoming HTTP request
     */
    public function handleRequest(Request $request): Response
    {
        try {
            // Boot service providers
            $this->bootProviders();
            
            // Get router from container
            $router = $this->make(Router::class);
            
            // Dispatch request through router
            $response = $router->dispatch($request);
            
            return $response;
            
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle an exception
     */
    protected function handleException(\Throwable $e): Response
    {
        // Log the exception
        $logger = $this->make(\Dew\MyFramework\Core\Services\Logger::class);
        $logger->error($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

        // Return error response
        if ($this->debug) {
            $handler = new ErrorHandler(true);
            return $handler->renderException($e);
        }

        return Response::serverError('An error occurred. Please try again later.');
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        // Register error handler
        $errorHandler = new ErrorHandler($this->debug);
        $errorHandler->register();

        // Capture request
        $request = Request::capture();
        
        // Handle request and get response
        $response = $this->handleRequest($request);
        
        // Send response
        $response->send();
    }

    /**
     * Get the framework version
     */
    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * Get the base path
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the container instance
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Resolve a service from the container
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->container->make($abstract, $parameters);
    }

    /**
     * Set debug mode
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Check if in debug mode
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Get environment (simple implementation)
     */
    public function environment(): string
    {
        return getenv('APP_ENV') ?: 'production';
    }

    /**
     * Check if running in production
     */
    public function isProduction(): bool
    {
        return $this->environment() === 'production';
    }

}