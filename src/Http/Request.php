<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http;

/**
 * Request
 * 
 * Represents an HTTP request with all its data
 */
class Request
{
    /**
     * Request URI (without query string)
     */
    private string $uri;

    /**
     * HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    private string $method;

    /**
     * Query parameters ($_GET)
     */
    private array $query;

    /**
     * Request body data ($_POST)
     */
    private array $request;

    /**
     * Uploaded files ($_FILES)
     */
    private array $files;

    /**
     * Cookies ($_COOKIE)
     */
    private array $cookies;

    /**
     * Server and execution environment ($_SERVER)
     */
    private array $server;

    /**
     * Request headers
     */
    private array $headers;

    /**
     * Create a new Request instance from global variables
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $files = [],
        array $cookies = [],
        array $server = []
    ) {
        $this->query = $query;
        $this->request = $request;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->server = $server;
        
        $this->uri = $this->parseUri();
        $this->method = $this->parseMethod();
        $this->headers = $this->parseHeaders();
    }

    /**
     * Create Request from PHP globals
     */
    public static function capture(): self
    {
        return new self(
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE,
            $_SERVER
        );
    }

    /**
     * Get the request URI (without query string)
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Get the request path (alias for uri)
     */
    public function path(): string
    {
        return $this->uri;
    }

    /**
     * Get the request method
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Check if request is GET
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Check if request is POST
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Check if request is PUT
     */
    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    /**
     * Check if request is DELETE
     */
    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    /**
     * Check if request is PATCH
     */
    public function isPatch(): bool
    {
        return $this->method === 'PATCH';
    }

    /**
     * Check if request method matches
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->method;
    }

    /**
     * Get query parameter (from URL)
     */
    public function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    /**
     * Get input from request body or query string
     */
    public function input(string $key = null, mixed $default = null): mixed
    {
        // Merge request and query, with request taking precedence
        $input = array_merge($this->query, $this->request);

        if ($key === null) {
            return $input;
        }

        return $input[$key] ?? $default;
    }

    /**
     * Get all input data
     */
    public function all(): array
    {
        return $this->input();
    }

    /**
     * Get only specified keys from input
     */
    public function only(array $keys): array
    {
        $input = $this->all();
        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $input)) {
                $result[$key] = $input[$key];
            }
        }

        return $result;
    }

    /**
     * Get all input except specified keys
     */
    public function except(array $keys): array
    {
        $input = $this->all();

        foreach ($keys as $key) {
            unset($input[$key]);
        }

        return $input;
    }

    /**
     * Check if input has a key
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    /**
     * Check if input has any of the given keys
     */
    public function hasAny(array $keys): bool
    {
        $input = $this->all();

        foreach ($keys as $key) {
            if (array_key_exists($key, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if input is missing a key
     */
    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    /**
     * Get uploaded file
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if request has file
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Get cookie value
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get request header
     */
    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get all headers
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get bearer token from Authorization header
     */
    public function bearerToken(): ?string
    {
        $header = $this->header('authorization', '');

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get client IP address
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return $this->header('x-requested-with') === 'XMLHttpRequest';
    }

    /**
     * Check if request expects JSON response
     */
    public function expectsJson(): bool
    {
        return $this->isAjax() || 
               str_contains($this->header('accept', ''), 'application/json');
    }

    /**
     * Get full URL
     */
    public function fullUrl(): string
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $uri = $this->server['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

    /**
     * Check if request is secure (HTTPS)
     */
    public function isSecure(): bool
    {
        return (
            isset($this->server['HTTPS']) && 
            strtolower($this->server['HTTPS']) !== 'off'
        ) || (
            isset($this->server['SERVER_PORT']) && 
            $this->server['SERVER_PORT'] == 443
        );
    }

    /**
     * Parse URI from server variables
     */
    private function parseUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Remove trailing slash (except for root)
        $uri = rtrim($uri, '/') ?: '/';

        return $uri;
    }

    /**
     * Parse HTTP method
     */
    private function parseMethod(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        // Check for method override (for PUT, PATCH, DELETE via POST)
        if ($method === 'POST' && isset($this->request['_method'])) {
            $method = strtoupper($this->request['_method']);
        }

        return $method;
    }

    /**
     * Parse headers from server variables
     */
    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($this->server as $key => $value) {
            // Headers start with HTTP_
            if (str_starts_with($key, 'HTTP_')) {
                // Convert HTTP_ACCEPT_LANGUAGE to accept-language
                $header = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$header] = $value;
            }
            // Also include CONTENT_TYPE and CONTENT_LENGTH
            elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $header = strtolower(str_replace('_', '-', $key));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get server variable
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Magic method to get input as property
     */
    public function __get(string $key): mixed
    {
        return $this->input($key);
    }

    /**
     * Magic method to check if input exists
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }
}