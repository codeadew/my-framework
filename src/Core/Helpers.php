<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

/**
 * Helpers
 * 
 * Collection of utility helper functions
 */
class Helpers
{
    /**
     * Dump and die - for debugging
     */
    public static function dd(mixed ...$vars): never
    {
        echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 10px;">';
        
        foreach ($vars as $var) {
            var_dump($var);
        }
        
        echo '</pre>';
        
        die(1);
    }

    /**
     * Get environment variable with fallback
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        return $value;
    }

    /**
     * Generate a random string
     */
    public static function randomString(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
}