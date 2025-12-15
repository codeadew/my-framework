<?php

declare(strict_types=1);

/**
 * Dew Framework - Front Controller
 * 
 * This is the single entry point for all HTTP requests.
 */

use Dew\MyFramework\Core\Application;

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer's autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Create and run the application
$app = new Application(BASE_PATH);

// Make globally accessible (for route closures)
$GLOBALS['app'] = $app;

// Run the application
$app->run();