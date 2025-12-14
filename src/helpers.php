<?php

declare(strict_types=1);

/**
 * Global Helper Functions
 * 
 * These functions are available globally without namespaces
 */

use Dew\MyFramework\Core\Helpers;

if (!function_exists('dd')) {
    /**
     * Dump and die
     */
    function dd(mixed ...$vars): never
    {
        Helpers::dd(...$vars);
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env(string $key, mixed $default = null): mixed
    {
        return Helpers::env($key, $default);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get base path
     */
    function base_path(string $path = ''): string
    {
        return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}