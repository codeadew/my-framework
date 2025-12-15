<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core\Services;

/**
 * Logger Service
 * 
 * Example service for demonstrating dependency injection
 */
class Logger
{
    private string $logPath;

    public function __construct(string $logPath = null)
    {
        $this->logPath = $logPath ?? BASE_PATH . '/storage/logs/app.log';
    }

    public function log(string $message, string $level = 'info'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
        
        // Ensure directory exists
        $dir = dirname($this->logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($this->logPath, $logEntry, FILE_APPEND);
    }

    public function info(string $message): void
    {
        $this->log($message, 'INFO');
    }

    public function error(string $message): void
    {
        $this->log($message, 'ERROR');
    }

    public function warning(string $message): void
    {
        $this->log($message, 'WARNING');
    }

    public function getLogPath(): string
    {
        return $this->logPath;
    }
}