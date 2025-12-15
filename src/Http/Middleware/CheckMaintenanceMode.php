<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Closure;

/**
 * CheckMaintenanceMode
 * 
 * Returns maintenance page if app is in maintenance mode
 */
class CheckMaintenanceMode implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceFile = BASE_PATH . '/storage/framework/down';
        
        if (file_exists($maintenanceFile)) {
            return $this->renderMaintenancePage();
        }

        return $next($request);
    }

    /**
     * Render maintenance mode page
     */
    protected function renderMaintenancePage(): Response
    {
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
        }
        .icon { font-size: 80px; margin-bottom: 20px; }
        h1 { color: #333; margin-bottom: 15px; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>We\'ll be right back!</h1>
        <p>The site is currently undergoing maintenance. Please check back soon.</p>
    </div>
</body>
</html>';

        return Response::html($html, 503);
    }
}