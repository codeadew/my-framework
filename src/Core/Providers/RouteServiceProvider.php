<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core\Providers;

use Dew\MyFramework\Core\ServiceProvider;
use Dew\MyFramework\Routing\Router;

/**
 * RouteServiceProvider
 * 
 * Registers the router and loads routes
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register the router service
     */
    public function register(): void
    {
        $this->app->container()->singleton(Router::class, function ($container) {
            return new Router();
        });
    }

    /**
     * Bootstrap the router by loading routes
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        
        // Load web routes
        $this->loadRoutes($router, $this->app->basePath('routes/web.php'));
        
        // You can add more route files here
        // $this->loadRoutes($router, $this->app->basePath('routes/api.php'));
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