<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core;

use Dew\MyFramework\Http\Response;
use Throwable;

/**
 * ErrorHandler
 * 
 * Handles errors and exceptions gracefully
 */
class ErrorHandler
{
    /**
     * Debug mode
     */
    private bool $debug;

    /**
     * Log file path
     */
    private string $logPath;

    /**
     * Create a new error handler
     */
    public function __construct(bool $debug = false, string $logPath = null)
    {
        $this->debug = $debug;
        $this->logPath = $logPath ?? BASE_PATH . '/storage/logs/error.log';
    }

    /**
     * Register error and exception handlers
     */
    public function register(): void
    {
        error_reporting(E_ALL);
        
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Handle PHP errors
     */
    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }

        return false;
    }

    /**
     * Handle uncaught exceptions
     */
    public function handleException(Throwable $e): void
    {
        // Log the error
        $this->logException($e);

        // Send response
        $response = $this->renderException($e);
        $response->send();
        
        exit(1);
    }

    /**
     * Handle fatal errors on shutdown
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->handleException(
                new \ErrorException(
                    $error['message'],
                    0,
                    $error['type'],
                    $error['file'],
                    $error['line']
                )
            );
        }
    }

    /**
     * Render exception as response
     */
    protected function renderException(Throwable $e): Response
    {
        $statusCode = $this->getStatusCode($e);

        if ($this->debug) {
            return Response::html($this->renderDebugPage($e), $statusCode);
        }

        return Response::html($this->renderProductionPage($statusCode), $statusCode);
    }

    /**
     * Get HTTP status code from exception
     */
    protected function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        return 500;
    }

    /**
     * Render debug error page (development)
     */
    protected function renderDebugPage(Throwable $e): string
    {
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile());
        $line = $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString());
        $class = get_class($e);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Dew Framework</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .error-header {
            background: #d32f2f;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .error-header .type {
            opacity: 0.9;
            font-size: 14px;
        }
        .error-body {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-body h2 {
            color: #4fc3f7;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .error-location {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 3px;
            margin: 10px 0;
        }
        .error-location .file {
            color: #ffab40;
        }
        .error-location .line {
            color: #66bb6a;
        }
        .stack-trace {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 3px;
            overflow-x: auto;
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.6;
        }
        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .back-link:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-header">
            <h1>⚠️ {$message}</h1>
            <div class="type">{$class}</div>
        </div>
        
        <div class="error-body">
            <h2>📍 Error Location</h2>
            <div class="error-location">
                <div class="file">File: {$file}</div>
                <div class="line">Line: {$line}</div>
            </div>
        </div>
        
        <div class="error-body">
            <h2>📚 Stack Trace</h2>
            <div class="stack-trace">{$trace}</div>
        </div>
        
        <a href="/" class="back-link">← Back to Home</a>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Render production error page
     */
    protected function renderProductionPage(int $statusCode): string
    {
        $title = $this->getStatusText($statusCode);
        $message = $this->getStatusMessage($statusCode);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$statusCode} {$title}</title>
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
        <div class="error-code">{$statusCode}</div>
        <div class="error-title">{$title}</div>
        <div class="error-message">{$message}</div>
        <a href="/" class="home-link">← Back to Home</a>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Log exception to file
     */
    protected function logException(Throwable $e): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $message = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            $timestamp,
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        // Ensure directory exists
        $dir = dirname($this->logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->logPath, $message, FILE_APPEND);
    }

    /**
     * Get HTTP status text
     */
    protected function getStatusText(int $code): string
    {
        $texts = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
        ];

        return $texts[$code] ?? 'Error';
    }

    /**
     * Get friendly status message
     */
    protected function getStatusMessage(int $code): string
    {
        $messages = [
            400 => 'The request could not be understood by the server.',
            401 => 'You need to authenticate to access this resource.',
            403 => 'You do not have permission to access this resource.',
            404 => 'The page you are looking for could not be found.',
            405 => 'The request method is not supported for this resource.',
            500 => 'An error occurred on the server. Please try again later.',
            502 => 'The server received an invalid response from an upstream server.',
            503 => 'The service is temporarily unavailable. Please try again later.',
        ];

        return $messages[$code] ?? 'An error occurred.';
    }
}   