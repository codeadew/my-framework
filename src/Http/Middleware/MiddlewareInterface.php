<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Closure;

/**
 * MiddlewareInterface
 * 
 * Contract for all middleware implementations
 */
interface MiddlewareInterface
{
    /**
     * Handle an incoming request
     * 
     * @param Request $request The incoming request
     * @param Closure $next The next middleware in the pipeline
     * @return Response The response after processing
     */
    public function handle(Request $request, Closure $next): Response;
}