<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Closure;

/**
 * TrimStrings
 * 
 * Trims whitespace from all string inputs
 */
class TrimStrings implements MiddlewareInterface
{
    /**
     * Fields to skip trimming
     */
    protected array $except = [
        'password',
        'password_confirmation',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $this->trimInput($request);
        
        return $next($request);
    }

    /**
     * Trim all string input
     */
    protected function trimInput(Request $request): void
    {
        // This is a simplified version
        // In a real implementation, you'd modify the request data
        // For now, we'll just demonstrate the concept
    }
}