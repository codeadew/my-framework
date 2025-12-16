<?php

declare(strict_types=1);

namespace Dew\MyFramework\Controllers;

use Dew\MyFramework\Core\Controller;
use Dew\MyFramework\Core\Services\UserService;
use Dew\MyFramework\Http\Response;

/**
 * UserController
 * 
 * Handles user-related operations
 */
class UserController extends Controller
{
    /**
     * Display list of users
     */
    public function index(): Response
    {
        // Get UserService from container (dependency injection!)
        $userService = $this->make(UserService::class);
        $users = $userService->getUsers();

        return $this->view('users/index', [
            'title' => 'All Users',
            'users' => $users,
        ]);
    }

    /**
     * Display a specific user
     */
    public function show(string $id): Response
    {
        $userService = $this->make(UserService::class);
        $user = $userService->getUserById((int)$id);

        if (!$user) {
            return $this->notFound('User not found');
        }

        return $this->view('users/show', [
            'title' => 'User Profile',
            'user' => $user,
        ]);
    }

    /**
     * Show create user form
     */
    public function create(): Response
    {
        return $this->view('users/create', [
            'title' => 'Create New User',
        ]);
    }

    /**
     * Store a new user
     */
    public function store(): Response
    {
        try {
            // Validate input
            $data = $this->validate([
                'name' => 'required|min:3|max:50',
                'email' => 'required|email',
            ]);

            // Create user
            $userService = $this->make(UserService::class);
            $user = $userService->createUser($data['name']);

            return $this->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);

        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => json_decode($e->getMessage(), true),
            ], 422);
        }
    }
}