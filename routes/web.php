<?php

declare(strict_types=1);

use Dew\MyFramework\Routing\Router;
use Dew\MyFramework\Core\Services\UserService;

return function (Router $router) {
    
    // Home page
    $router->get('/', function ($request) {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Dew Framework - Application Core</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        h1 { color: #667eea; }
        h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        ul { line-height: 2; }
        a { color: #667eea; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .feature { background: #f4f4f4; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🎉 Dew Framework v1.0.0-dev</h1>
    
    <div class="success">
        <strong>✅ Application Core Integrated!</strong>
        <p>The framework now has a complete request lifecycle with proper error handling.</p>
    </div>
    
    <h2>🚀 Features</h2>
    <div class="feature">
        <strong>Request Lifecycle:</strong> Request → Router → Controller → Response
    </div>
    <div class="feature">
        <strong>Service Providers:</strong> Modular service registration and bootstrapping
    </div>
    <div class="feature">
        <strong>Error Handling:</strong> Graceful error pages with stack traces in debug mode
    </div>
    <div class="feature">
        <strong>Dependency Injection:</strong> Automatic resolution of dependencies
    </div>
    
    <h2>🧪 Test Routes</h2>
    <ul>
        <li><a href="/about">About Page</a></li>
        <li><a href="/users">User Service (DI Example)</a></li>
        <li><a href="/api/status">API Status (JSON)</a></li>
    </ul>
    
    <h2>⚠️ Error Handling Tests</h2>
    <ul>
        <li><a href="/error/exception">Test Exception (Debug Mode)</a></li>
        <li><a href="/error/404">Test 404 Not Found</a></li>
        <li><a href="/nonexistent">Actual 404 Page</a></li>
    </ul>
    
    <h2>📊 Application Info</h2>
    <ul>
        <li><strong>Environment:</strong> ' . (getenv('APP_ENV') ?: 'development') . '</li>
        <li><strong>Debug Mode:</strong> Enabled</li>
        <li><strong>Base Path:</strong> ' . BASE_PATH . '</li>
    </ul>
    
</body>
</html>';
    })->name('home');

    // About page
    $router->get('/about', function ($request) {
        return '<h1>About Dew Framework</h1>
                <p>A modern PHP framework built from scratch with:</p>
                <ul>
                    <li>Routing with parameter constraints</li>
                    <li>Dependency Injection Container</li>
                    <li>Request/Response handling</li>
                    <li>Error handling</li>
                    <li>Service Providers</li>
                </ul>
                <a href="/">← Back to Home</a>';
    })->name('about');

    // User service example (with DI)
    $router->get('/users', function ($request) {
        global $app;
        
        // UserService and all its dependencies are auto-injected
        $userService = $app->make(UserService::class);
        $users = $userService->getUsers();
        
        $html = '<h1>Users (Dependency Injection Example)</h1>
                 <p>UserService automatically resolved with Database and Logger injected!</p>
                 <ul>';
        
        foreach ($users as $user) {
            $html .= '<li>' . $user['name'] . ' (ID: ' . $user['id'] . ')</li>';
        }
        
        $html .= '</ul><a href="/">← Back to Home</a>';
        
        return $html;
    })->name('users.index');

    // API status endpoint
    $router->get('/api/status', function ($request) {
        return [
            'status' => 'ok',
            'framework' => 'Dew Framework',
            'version' => '1.0.0-dev',
            'timestamp' => time(),
            'environment' => getenv('APP_ENV') ?: 'development',
        ];
    })->name('api.status');

    // Test exception handling
    $router->get('/error/exception', function ($request) {
        throw new \RuntimeException('This is a test exception to demonstrate error handling!');
    });

    // Test 404 error
    $router->get('/error/404', function ($request) {
        return \Dew\MyFramework\Http\Response::notFound('This page intentionally returns 404');
    });
};