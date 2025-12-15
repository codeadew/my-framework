<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core\Services;

/**
 * Database Service
 * 
 * Example service that depends on Logger
 */
class Database
{
    private Logger $logger;
    private string $host;
    private string $database;

    /**
     * Constructor - Logger is automatically injected!
     */
    public function __construct(Logger $logger, string $host = 'localhost', string $database = 'myapp')
    {
        $this->logger = $logger;
        $this->host = $host;
        $this->database = $database;
        
        $this->logger->info("Database service initialized for {$this->database}@{$this->host}");
    }

    public function query(string $sql): array
    {
        $this->logger->info("Executing query: $sql");
        
        // Simulated query result
        return [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith'],
        ];
    }

    public function getConnectionInfo(): array
    {
        return [
            'host' => $this->host,
            'database' => $this->database,
        ];
    }
}