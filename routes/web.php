<?php

declare(strict_types=1);

use Dew\MyFramework\Routing\Router;

return function (Router $router) {
    
    // Home page
    $router->get('/', function ($request) {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Dew Framework - Middleware System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        h1 { color: #667eea; }
        h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        ul { line-height: 2; }
        a { color: #667eea; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .feature { background: #f4f4f4; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
        code { background: #1e1e1e; color: #dcdcdc; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🎉 Dew Framework - Middleware System</h1>
    
    <div class="success">
        <strong>✅ Middleware System Active!</strong>
        <p>Requests now pass through a middleware pipeline for flexible processing.</p>
    </div>
    
    <h2>🧅 The Middleware Onion</h2>
    <div class="feature">
        <p>Middleware wraps your application like layers of an onion:</p>
        <pre>Request → Middleware 1 → Middleware 2 → Route Handler → Middleware 2 → Middleware 1 → Response</pre>
    </div>
    
    <h2>🧪 Test Middleware</h2>
    <ul>
        <li><a href="/middleware/logging">Logging Middleware</a> - Logs request/response</li>
        <li><a href="/middleware/headers">Custom Headers</a> - Adds security headers</li>
        <li><a href="/middleware/protected">Protected Route</a> - Requires authentication</li>
        <li><a href="/middleware/group">Middleware Group</a> - Multiple middleware</li>
        <li><a href="/api/throttle">Rate Limiting</a> - Try multiple requests</li>
    </ul>
    
    <h2>🔧 Maintenance Mode</h2>
    <ul>
        <li><a href="/maintenance/enable">Enable Maintenance Mode</a></li>
        <li><a href="/maintenance/disable">Disable Maintenance Mode</a></li>
    </ul>
    
    <h2>📊 Global Middleware</h2>
    <div class="feature">
        <p>These run on <strong>every</strong> request:</p>
        <ul>
            <li>✅ CheckMaintenanceMode</li>
            <li>✅ TrimStrings</li>
        </ul>
    </div>
    
    <h2>💡 Check the Logs!</h2>
    <p>Visit any route and check <code>storage/logs/app.log</code> to see middleware logging in action.</p>
    
</body>
</html>';
    })->name('home');

    // Logging middleware example
    $router->get('/middleware/logging', function ($request) {
        return '<h1>Logging Middleware Test</h1>
                <p>This request was logged before and after processing!</p>
                <p>Check <code>storage/logs/app.log</code> to see the entries.</p>
                <a href="/">← Back to Home</a>';
    })->middleware('log');

    // Headers middleware example
    $router->get('/middleware/headers', function ($request) {
        return '<h1>Custom Headers Test</h1>
                <p>This response includes custom security headers.</p>
                <p>Open DevTools → Network tab and check the response headers:</p>
                <ul>
                    <li>X-Frame-Options</li>
                    <li>X-Content-Type-Options</li>
                    <li>X-XSS-Protection</li>
                    <li>X-Powered-By</li>
                </ul>
                <a href="/">← Back to Home</a>';
    })->middleware('headers');

    // Protected route (auth middleware)
    $router->get('/middleware/protected', function ($request) {
        return '<h1>Protected Route</h1>
                <p>✅ You are authenticated!</p>
                <p>This page is only accessible with an auth_token cookie.</p>
                <a href="/">← Back to Home</a>';
    })->middleware('auth');

    // Multiple middleware (group)
    $router->get('/middleware/group', function ($request) {
        return '<h1>Middleware Group Test</h1>
                <p>This route uses multiple middleware:</p>
                <ul>
                    <li>✅ Logging</li>
                    <li>✅ Custom Headers</li>
                </ul>
                <p>Check logs and response headers!</p>
                <a href="/">← Back to Home</a>';
    })->middleware(['log', 'headers']);

    // API with rate limiting
    $router->get('/api/throttle', function ($request) {
        return [
            'success' => true,
            'message' => 'API request successful',
            'timestamp' => time(),
            'note' => 'Try refreshing this page 60+ times in one minute to trigger rate limit',
        ];
    })->middleware('throttle');

    // Maintenance mode controls
    $router->get('/maintenance/enable', function ($request) {
        $dir = BASE_PATH . '/storage/framework';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($dir . '/down', 'Maintenance mode enabled');
        
        return '<h1>Maintenance Mode Enabled</h1>
                <p>The application is now in maintenance mode.</p>
                <p><strong>Note:</strong> This route still works, but try visiting <a href="/">the homepage</a>.</p>
                <p><a href="/maintenance/disable">Disable Maintenance Mode</a></p>';
    });

    $router->get('/maintenance/disable', function ($request) {
        $file = BASE_PATH . '/storage/framework/down';
        if (file_exists($file)) {
            unlink($file);
        }
        
        return '<h1>Maintenance Mode Disabled</h1>
                <p>The application is back online!</p>
                <a href="/">← Back to Home</a>';
    });

    // Middleware group example (admin section)
    $router->group(['prefix' => 'admin', 'middleware' => ['auth', 'log']], function ($router) {
        
        $router->get('/dashboard', function ($request) {
            return '<h1>Admin Dashboard</h1>
                    <p>This entire admin section is protected by auth middleware.</p>
                    <a href="/">← Back to Home</a>';
        });

        $router->get('/users', function ($request) {
            return '<h1>Admin Users</h1>
                    <p>Manage users here (protected route).</p>
                    <a href="/">← Back to Home</a>';
        });
    });
};