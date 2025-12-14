<?php

declare(strict_types=1);

/**
 * Dew Framework - Front Controller
 * 
 * This is the single entry point for all HTTP requests.
 * All requests are routed through this file.
 */

// Define base path constant
define('BASE_PATH', dirname(__DIR__));

// Load Composer's autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// For now, let's test that everything works
echo "🎉 Dew Framework is running!<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Base Path: " . BASE_PATH . "<br>";

// Display loaded namespaces
echo "<br><strong>Autoload Status:</strong><br>";
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    echo "✅ Composer autoloader found and loaded<br>";
} else {
    echo "❌ Composer autoloader NOT found<br>";
}

echo "<br><strong>Directory Structure:</strong><br>";
echo "✅ Public directory: " . __DIR__ . "<br>";
echo "✅ Framework root: " . BASE_PATH . "<br>";