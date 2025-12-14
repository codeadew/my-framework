<?php

declare(strict_types=1);

/**
 * Dew Framework - Front Controller
 * 
 * This is the single entry point for all HTTP requests.
 */

use Dew\MyFramework\Core\Application;
use Dew\MyFramework\Core\Helpers;

// Define base path constant
define('BASE_PATH', dirname(__DIR__));

// Load Composer's autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Test autoloading by creating an Application instance
$app = new Application(BASE_PATH);

// Run the application
$app->run();

// Display some debug information
echo "<hr>";
echo "<h2>Autoloading Tests</h2>";

// Test 1: Application class loaded
echo "<p>✅ <strong>Test 1:</strong> Application class instantiated successfully</p>";

// Test 2: Check version method
echo "<p>✅ <strong>Test 2:</strong> Version method works: " . $app->version() . "</p>";

// Test 3: Check basePath method
echo "<p>✅ <strong>Test 3:</strong> Base path method works: " . $app->basePath() . "</p>";

// Test 4: Test Helpers class
$randomStr = Helpers::randomString(10);
echo "<p>✅ <strong>Test 4:</strong> Helpers class works - Random string: <code>" . $randomStr . "</code></p>";

// Test 5: Test env helper
$phpVersion = Helpers::env('PHP_VERSION', 'Unknown');
echo "<p>✅ <strong>Test 5:</strong> Environment helper works - PHP Version: " . $phpVersion . "</p>";

echo "<hr>";
echo "<h3 style='color: green;'>🎉 All autoloading tests passed!</h3>";

// Uncomment to test the dd() helper (this will stop execution)
// Helpers::dd($app, 'Testing dd() helper', ['array' => 'test']);