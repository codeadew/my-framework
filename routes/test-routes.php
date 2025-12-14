<?php

/**
 * Route Testing Utility
 * 
 * Run this to see all registered routes and their patterns
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dew\MyFramework\Routing\Router;

$router = new Router();

// Load routes
$routeLoader = require __DIR__ . '/web.php';
$routeLoader($router);

echo "=== REGISTERED ROUTES ===\n\n";

foreach ($router->getRoutes() as $route) {
    echo "Method: " . $route->getMethod() . "\n";
    echo "URI: " . $route->getUri() . "\n";
    echo "Regex: " . $route->getRegex() . "\n";
    echo "Name: " . ($route->getName() ?? 'unnamed') . "\n";
    
    if (!empty($route->getConstraints())) {
        echo "Constraints: " . json_encode($route->getConstraints()) . "\n";
    }
    
    if (!empty($route->getOptionalParameters())) {
        echo "Optional Params: " . implode(', ', $route->getOptionalParameters()) . "\n";
    }
    
    if (!empty($route->getDefaults())) {
        echo "Defaults: " . json_encode($route->getDefaults()) . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}