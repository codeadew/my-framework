<?php

declare(strict_types=1);

use Dew\MyFramework\Core\Application;
use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Dew\MyFramework\Routing\Router;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Create application instance
$app = new Application(BASE_PATH);

// Create router instance
$router = new Router();

// Load routes from configuration file
$routeLoader = require BASE_PATH . '/routes/web.php';
$routeLoader($router);

// Capture the HTTP request
$request = Request::capture();

// Dispatch the request through the router
$response = $router->dispatch($request);

// Send the response
$response->send();