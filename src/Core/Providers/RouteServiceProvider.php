<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core\Providers;

use Dew\MyFramework\Core\ServiceProvider;
use Dew\MyFramework\Routing\Router;

/**
 * RouteServiceProvider
 * 
 * Registers the router and configures middleware
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register the router service
     */
    public function register(): void
    {
        $this->app->container()->singleton(Router::class, function ($container) {
            $router = new Router();
            $router->setContainer($container);
            return $router;
        });
    }

    /**
     * Bootstrap the router
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        
        // Register global middleware
        $this->registerGlobalMiddleware($router);
        
        // Register middleware aliases
        $this->registerMiddlewareAliases($router);
        
        // Load routes
        $this->loadRoutes($router, $this->app->basePath('routes/web.php'));
    }

    /**
     * Register global middleware (runs on every request)
     */
    protected function registerGlobalMiddleware(Router $router): void
    {
        $router->addGlobalMiddleware(\Dew\MyFramework\Http\Middleware\CheckMaintenanceMode::class);
        $router->addGlobalMiddleware(\Dew\MyFramework\Http\Middleware\TrimStrings::class);
    }

    /**
     * Register middleware aliases
     */
    protected function registerMiddlewareAliases(Router $router): void
    {
        $router->aliasMiddleware('log', \Dew\MyFramework\Http\Middleware\LogRequests::class);
        $router->aliasMiddleware('headers', \Dew\MyFramework\Http\Middleware\AddHeaders::class);
        $router->aliasMiddleware('auth', \Dew\MyFramework\Http\Middleware\Authenticate::class);
        $router->aliasMiddleware('throttle', \Dew\MyFramework\Http\Middleware\RateLimiter::class);
    }

    /**
     * Load routes from a file
     */
    protected function loadRoutes(Router $router, string $path): void
    {
        if (file_exists($path)) {
            $routeLoader = require $path;
            if (is_callable($routeLoader)) {
                $routeLoader($router);
            }
        }
    }
}