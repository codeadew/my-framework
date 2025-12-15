<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Closure;

/**
 * AddHeaders
 * 
 * Adds security and custom headers to responses
 */
class AddHeaders implements MiddlewareInterface
{
    /**
     * Headers to add
     */
    private array $headers = [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'X-Powered-By' => 'Dew Framework',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Process request first
        $response = $next($request);

        // Add headers to response
        foreach ($this->headers as $key => $value) {
            $response->setHeader($key, $value);
        }

        return $response;
    }
}