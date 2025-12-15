<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

/**
 * ServiceProvider
 * 
 * Base class for all service providers
 */
abstract class ServiceProvider
{
    /**
     * The application instance
     */
    protected Application $app;

    /**
     * Create a new service provider instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register any application services
     */
    abstract public function register(): void;

    /**
     * Bootstrap any application services
     */
    public function boot(): void
    {
        // Optional - override if needed
    }
}