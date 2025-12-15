<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Dew\MyFramework\Core\Services\Logger;
use Closure;

/**
 * LogRequests
 * 
 * Logs all incoming requests
 */
class LogRequests implements MiddlewareInterface
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Log before processing
        $this->logger->info(sprintf(
            'Request: %s %s from %s',
            $request->method(),
            $request->uri(),
            $request->ip()
        ));

        // Process request
        $response = $next($request);

        // Log after processing
        $this->logger->info(sprintf(
            'Response: %s %s returned %d',
            $request->method(),
            $request->uri(),
            $response->getStatusCode()
        ));

        return $response;
    }
}