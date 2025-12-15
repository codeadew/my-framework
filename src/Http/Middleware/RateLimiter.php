<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http\Middleware;

use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;
use Closure;

/**
 * RateLimiter
 * 
 * Simple rate limiting middleware
 */
class RateLimiter implements MiddlewareInterface
{
    /**
     * Maximum requests per minute
     */
    private int $maxRequests = 60;

    /**
     * Storage for request counts
     */
    private array $requests = [];

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestKey($request);
        
        // Get current minute
        $minute = floor(time() / 60);
        
        // Initialize or increment counter
        if (!isset($this->requests[$key][$minute])) {
            $this->requests[$key] = [$minute => 1];
        } else {
            $this->requests[$key][$minute]++;
        }

        // Check if limit exceeded
        if ($this->requests[$key][$minute] > $this->maxRequests) {
            return $this->buildRateLimitResponse();
        }

        return $next($request);
    }

    /**
     * Resolve the rate limit key for the request
     */
    protected function resolveRequestKey(Request $request): string
    {
        return $request->ip();
    }

    /**
     * Build rate limit exceeded response
     */
    protected function buildRateLimitResponse(): Response
    {
        return Response::json([
            'error' => 'Too many requests',
            'message' => 'Rate limit exceeded. Please try again later.',
        ], 429)
        ->setHeader('Retry-After', '60');
    }
}