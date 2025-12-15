<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Closure;

/**
 * Authenticate
 * 
 * Ensures the user is authenticated
 */
class Authenticate implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated (simplified check)
        $authenticated = $request->cookie('auth_token') !== null;

        if (!$authenticated) {
            // Return unauthorized response
            return Response::unauthorized('You must be logged in to access this page.');
        }

        return $next($request);
    }
}