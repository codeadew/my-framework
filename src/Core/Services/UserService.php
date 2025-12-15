<?php

declare(strict_types=1);

namespace Dew\MyFramework\Core\Services;

/**
 * UserService
 * 
 * Example service with nested dependencies (Database depends on Logger)
 */
class UserService
{
    private Database $database;
    private Logger $logger;

    /**
     * Both dependencies are automatically injected!
     */
    public function __construct(Database $database, Logger $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
        
        $this->logger->info("UserService initialized");
    }

    public function getUsers(): array
    {
        $this->logger->info("Fetching all users");
        return $this->database->query("SELECT * FROM users");
    }

    public function getUserById(int $id): ?array
    {
        $this->logger->info("Fetching user with ID: $id");
        $users = $this->database->query("SELECT * FROM users WHERE id = $id");
        return $users[0] ?? null;
    }

    public function createUser(string $name): array
    {
        $this->logger->info("Creating new user: $name");
        
        return [
            'id' => rand(100, 999),
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}