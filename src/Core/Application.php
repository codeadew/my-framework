<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

/**
 * Application
 * 
 * The core application class that bootstraps and runs the framework.
 * This will be expanded in later steps.
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
     * Create a new application instance
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
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
     * Run the application (placeholder for now)
     */
    public function run(): void
    {
        echo "<h1>Dew Framework v" . $this->version() . "</h1>";
        echo "<p>Application is running from: " . $this->basePath() . "</p>";
        echo "<p>✅ Autoloading is working correctly!</p>";
    }
}