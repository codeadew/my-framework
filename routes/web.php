<?php

declare(strict_types=1);

use Dew\MyFramework\Routing\Router;

/**
 * Web Routes
 * 
 * Define your application routes here
 */

return function (Router $router) {
    
    // Home page
    $router->get('/', function ($request) {
        return '<h1>Welcome to Dew Framework!</h1>
                <p>Router is working! Try these routes:</p>
                <ul>
                    <li><a href="/about">About Page</a></li>
                    <li><a href="/users">Users List</a></li>
                    <li><a href="/users/123">User Profile (ID: 123)</a></li>
                    <li><a href="/posts/456/comments/789">Post Comment</a></li>
                    <li><a href="/api/products">API Products (JSON)</a></li>
                    <li><a href="/dashboard">Dashboard (Route Group)</a></li>
                    <li><a href="/dashboard/settings">Settings (Route Group)</a></li>
                </ul>';
    })->name('home');

    // About page
    $router->get('/about', function ($request) {
        return '<h1>About Dew Framework</h1>
                <p>A modern PHP framework built from scratch.</p>
                <a href="/">← Back to Home</a>';
    })->name('about');

    // Users list
    $router->get('/users', function ($request) {
        $users = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith'],
            ['id' => 3, 'name' => 'Bob Johnson'],
        ];

        $html = '<h1>Users List</h1><ul>';
        foreach ($users as $user) {
            $html .= '<li><a href="/users/' . $user['id'] . '">' . $user['name'] . '</a></li>';
        }
        $html .= '</ul><a href="/">← Back to Home</a>';

        return $html;
    })->name('users.index');

    // User profile (with parameter)
    $router->get('/users/{id}', function ($request, $id) {
        return '<h1>User Profile</h1>
                <p>Viewing profile for user ID: <strong>' . htmlspecialchars($id) . '</strong></p>
                <a href="/users">← Back to Users</a>';
    })->name('users.show');

    // Post with comment (multiple parameters)
    $router->get('/posts/{postId}/comments/{commentId}', function ($request, $postId, $commentId) {
        return '<h1>Post Comment</h1>
                <p>Post ID: <strong>' . htmlspecialchars($postId) . '</strong></p>
                <p>Comment ID: <strong>' . htmlspecialchars($commentId) . '</strong></p>
                <a href="/">← Back to Home</a>';
    })->name('posts.comments.show');

    // API endpoint (returns JSON)
    $router->get('/api/products', function ($request) {
        $products = [
            ['id' => 1, 'name' => 'Laptop', 'price' => 999.99],
            ['id' => 2, 'name' => 'Mouse', 'price' => 29.99],
            ['id' => 3, 'name' => 'Keyboard', 'price' => 79.99],
        ];

        return $products; // Automatically converted to JSON
    })->name('api.products');

    // POST example (form submission)
    $router->post('/contact', function ($request) {
        $name = $request->input('name', 'Guest');
        $email = $request->input('email', 'No email');
        $message = $request->input('message', 'No message');

        return [
            'success' => true,
            'message' => 'Contact form received',
            'data' => [
                'name' => $name,
                'email' => $email,
                'message' => $message
            ]
        ];
    })->name('contact.submit');

    // Route group example (with prefix)
    $router->group(['prefix' => 'dashboard'], function ($router) {
        
        $router->get('/', function ($request) {
            return '<h1>Dashboard Home</h1>
                    <p>Welcome to your dashboard!</p>
                    <ul>
                        <li><a href="/dashboard/profile">Profile</a></li>
                        <li><a href="/dashboard/settings">Settings</a></li>
                    </ul>
                    <a href="/">← Back to Home</a>';
        })->name('dashboard.home');

        $router->get('/profile', function ($request) {
            return '<h1>Dashboard Profile</h1>
                    <p>Your profile information</p>
                    <a href="/dashboard">← Back to Dashboard</a>';
        })->name('dashboard.profile');

        $router->get('/settings', function ($request) {
            return '<h1>Dashboard Settings</h1>
                    <p>Manage your settings</p>
                    <a href="/dashboard">← Back to Dashboard</a>';
        })->name('dashboard.settings');
    });

    // Multiple HTTP methods
    $router->match(['GET', 'POST'], '/form', function ($request) {
        if ($request->isPost()) {
            return ['message' => 'Form submitted!', 'data' => $request->all()];
        }

        return '<h1>Contact Form</h1>
                <form method="POST" action="/form">
                    <input type="text" name="name" placeholder="Name"><br><br>
                    <input type="email" name="email" placeholder="Email"><br><br>
                    <textarea name="message" placeholder="Message"></textarea><br><br>
                    <button type="submit">Submit</button>
                </form>
                <a href="/">← Back to Home</a>';
    })->name('form');
};