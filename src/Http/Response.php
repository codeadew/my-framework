<?php

declare(strict_types=1);

namespace Dew\MyFramework\Http;

/**
 * Response
 * 
 * Represents an HTTP response to be sent to the client
 */
class Response
{
    /**
     * HTTP status code
     */
    private int $statusCode;

    /**
     * Response content
     */
    private string $content;

    /**
     * Response headers
     */
    private array $headers;

    /**
     * HTTP status texts
     */
    private const STATUS_TEXTS = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    /**
     * Create a new Response instance
     */
    public function __construct(
        string $content = '',
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set response content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get response content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Get HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set a header
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Get a header
     */
    public function getHeader(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Send the response to the client
     */
    public function send(): void
    {
        // Send status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Send content
        echo $this->content;
    }

    /**
     * Create a plain text response
     */
    public static function text(string $content, int $statusCode = 200): self
    {
        return new self($content, $statusCode, [
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }

    /**
     * Create an HTML response
     */
    public static function html(string $content, int $statusCode = 200): self
    {
        return new self($content, $statusCode, [
            'Content-Type' => 'text/html; charset=UTF-8'
        ]);
    }

    /**
     * Create a JSON response
     */
    public static function json(mixed $data, int $statusCode = 200, int $options = 0): self
    {
        $content = json_encode($data, $options | JSON_THROW_ON_ERROR);

        return new self($content, $statusCode, [
            'Content-Type' => 'application/json; charset=UTF-8'
        ]);
    }

    /**
     * Create a redirect response
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        return new self('', $statusCode, [
            'Location' => $url
        ]);
    }

    /**
     * Create a file download response
     */
    public static function download(string $filePath, string $name = null): self
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: $filePath");
        }

        $name = $name ?? basename($filePath);
        $content = file_get_contents($filePath);

        return new self($content, 200, [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => "attachment; filename=\"$name\"",
            'Content-Length' => (string) filesize($filePath),
        ]);
    }

    /**
     * Create a 404 Not Found response
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return self::html(
            self::errorTemplate(404, $message),
            404
        );
    }

    /**
     * Create a 500 Internal Server Error response
     */
    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return self::html(
            self::errorTemplate(500, $message),
            500
        );
    }

    /**
     * Create a 403 Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::html(
            self::errorTemplate(403, $message),
            403
        );
    }

    /**
     * Create a 401 Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::html(
            self::errorTemplate(401, $message),
            401
        );
    }

    /**
     * Set no-cache headers
     */
    public function noCache(): self
    {
        $this->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->setHeader('Pragma', 'no-cache');
        $this->setHeader('Expires', '0');
        return $this;
    }

    /**
     * Set CORS headers
     */
    public function cors(
        string $origin = '*',
        string $methods = 'GET, POST, PUT, DELETE, OPTIONS',
        string $headers = 'Content-Type, Authorization'
    ): self {
        $this->setHeader('Access-Control-Allow-Origin', $origin);
        $this->setHeader('Access-Control-Allow-Methods', $methods);
        $this->setHeader('Access-Control-Allow-Headers', $headers);
        return $this;
    }

    /**
     * Set cookie
     */
    public function cookie(
        string $name,
        string $value,
        int $expire = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = true
    ): self {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        return $this;
    }

    /**
     * Get HTTP status text
     */
    public function getStatusText(): string
    {
        return self::STATUS_TEXTS[$this->statusCode] ?? 'Unknown Status';
    }

    /**
     * Check if response is successful (2xx)
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if response is a redirect (3xx)
     */
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Check if response is a client error (4xx)
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if response is a server error (5xx)
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Generate error page template
     */
    private static function errorTemplate(int $code, string $message): string
    {
        $statusText = self::STATUS_TEXTS[$code] ?? 'Error';

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>$code $statusText</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 10px;
                    padding: 60px 40px;
                    text-align: center;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    max-width: 500px;
                }
                .error-code {
                    font-size: 80px;
                    font-weight: bold;
                    color: #667eea;
                    margin-bottom: 20px;
                }
                .error-title {
                    font-size: 24px;
                    color: #333;
                    margin-bottom: 15px;
                }
                .error-message {
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                .home-link {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #667eea;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: 600;
                    transition: background 0.3s;
                }
                .home-link:hover {
                    background: #5568d3;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-code">$code</div>
                <div class="error-title">$statusText</div>
                <div class="error-message">$message</div>
                <a href="/" class="home-link">← Back to Home</a>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Convert response to string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}