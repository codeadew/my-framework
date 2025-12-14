<?php

declare(strict_types=1);

use Dew\MyFramework\Routing\Router;

/**
 * Web Routes with Advanced Parameter Features
 */

return function (Router $router) {
    
    // ============================================
    // BASIC ROUTES
    // ============================================
    
    $router->get('/', function ($request) {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Dew Framework - Advanced Routing</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        h1 { color: #667eea; }
        h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        ul { line-height: 2; }
        a { color: #667eea; text-decoration: none; }
        a:hover { text-decoration: underline; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🎉 Dew Framework - Advanced Routing</h1>
    
    <h2>📝 Basic Routes</h2>
    <ul>
        <li><a href="/about">About Page</a></li>
        <li><a href="/contact">Contact Page</a></li>
    </ul>
    
    <h2>🔢 Parameter Constraints</h2>
    <ul>
        <li><a href="/users/123">User by ID (numeric only)</a> - <code>/users/{id}</code></li>
        <li><a href="/users/abc">User by ID (invalid - try it!)</a></li>
        <li><a href="/products/laptop-pro-2024">Product by Slug</a> - <code>/products/{slug}</code></li>
        <li><a href="/posts/2024/12/15">Blog Post by Date</a> - <code>/posts/{year}/{month}/{day}</code></li>
    </ul>
    
    <h2>❓ Optional Parameters</h2>
    <ul>
        <li><a href="/search">Search (no term)</a></li>
        <li><a href="/search/laptop">Search for "laptop"</a></li>
        <li><a href="/blog/2024">Blog Archive (year only)</a></li>
        <li><a href="/blog/2024/12">Blog Archive (year + month)</a></li>
        <li><a href="/blog/2024/12/15">Blog Archive (full date)</a></li>
    </ul>
    
    <h2>🎯 Default Values</h2>
    <ul>
        <li><a href="/page">Default Page (page 1)</a></li>
        <li><a href="/page/3">Page 3</a></li>
        <li><a href="/category">All Categories</a></li>
        <li><a href="/category/electronics">Electronics Category</a></li>
    </ul>
    
    <h2>🔐 Multiple Constraints</h2>
    <ul>
        <li><a href="/orders/12345">Order by Number</a> - <code>/orders/{orderId}</code></li>
        <li><a href="/api/v1/users">API Version 1</a></li>
        <li><a href="/api/v2/users">API Version 2</a></li>
    </ul>
    
</body>
</html>';
    })->name('home');

    // ============================================
    // PARAMETER CONSTRAINTS
    // ============================================
    
    // User by ID (numeric only)
    $router->get('/users/{id}', function ($request, $id) {
        return '<h1>User Profile</h1>
                <p>User ID: <strong>' . htmlspecialchars($id) . '</strong></p>
                <p>✅ This ID is numeric!</p>
                <a href="/">← Back to Home</a>';
    })
    ->whereNumber('id')
    ->name('users.show');

    // Product by slug (lowercase, numbers, hyphens only)
    $router->get('/products/{slug}', function ($request, $slug) {
        return '<h1>Product Details</h1>
                <p>Product Slug: <strong>' . htmlspecialchars($slug) . '</strong></p>
                <p>✅ Valid slug format!</p>
                <a href="/">← Back to Home</a>';
    })
    ->whereSlug('slug')
    ->name('products.show');

    // Blog post by date (all numeric)
    $router->get('/posts/{year}/{month}/{day}', function ($request, $year, $month, $day) {
        return '<h1>Blog Post</h1>
                <p>Date: <strong>' . htmlspecialchars("$year-$month-$day") . '</strong></p>
                <p>Year: ' . $year . ' (4 digits)</p>
                <p>Month: ' . $month . ' (2 digits)</p>
                <p>Day: ' . $day . ' (2 digits)</p>
                <a href="/">← Back to Home</a>';
    })
    ->where([
        'year' => '[0-9]{4}',
        'month' => '[0-9]{2}',
        'day' => '[0-9]{2}'
    ])
    ->name('posts.date');

    // ============================================
    // OPTIONAL PARAMETERS
    // ============================================
    
    // Search with optional term
    $router->get('/search/{term?}', function ($request, $term = null) {
        if ($term) {
            return '<h1>Search Results</h1>
                    <p>Searching for: <strong>' . htmlspecialchars($term) . '</strong></p>
                    <p>Found 42 results!</p>
                    <a href="/">← Back to Home</a>';
        }
        
        return '<h1>Search</h1>
                <form action="/search" method="get">
                    <input type="text" name="term" placeholder="Enter search term...">
                    <button type="submit">Search</button>
                </form>
                <a href="/">← Back to Home</a>';
    })
    ->name('search');

    // Blog archive with optional month and day
    $router->get('/blog/{year}/{month?}/{day?}', function ($request, $year, $month = null, $day = null) {
        $html = '<h1>Blog Archive</h1>';
        $html .= '<p>Year: <strong>' . htmlspecialchars($year) . '</strong></p>';
        
        if ($month) {
            $html .= '<p>Month: <strong>' . htmlspecialchars($month) . '</strong></p>';
        }
        
        if ($day) {
            $html .= '<p>Day: <strong>' . htmlspecialchars($day) . '</strong></p>';
        }
        
        $html .= '<p>Showing ' . ($day ? 'daily' : ($month ? 'monthly' : 'yearly')) . ' archive</p>';
        $html .= '<a href="/">← Back to Home</a>';
        
        return $html;
    })
    ->whereNumber('year')
    ->whereNumber('month')
    ->whereNumber('day')
    ->name('blog.archive');

    // ============================================
    // DEFAULT VALUES
    // ============================================
    
    // Pagination with default page
    $router->get('/page/{number?}', function ($request, $number) {
        return '<h1>Page ' . htmlspecialchars($number) . '</h1>
                <p>Showing page <strong>' . $number . '</strong> of results</p>
                <ul>
                    <li><a href="/page/1">Page 1</a></li>
                    <li><a href="/page/2">Page 2</a></li>
                    <li><a href="/page/3">Page 3</a></li>
                </ul>
                <a href="/">← Back to Home</a>';
    })
    ->whereNumber('number')
    ->defaults(['number' => '1'])
    ->name('page');

    // Category with default "all"
    $router->get('/category/{name?}', function ($request, $name) {
        $categories = ['electronics', 'clothing', 'books', 'toys'];
        
        $html = '<h1>Category: ' . htmlspecialchars(ucfirst($name)) . '</h1>';
        
        if ($name === 'all') {
            $html .= '<p>Showing all categories</p>';
        } else {
            $html .= '<p>Showing items in: <strong>' . htmlspecialchars($name) . '</strong></p>';
        }
        
        $html .= '<ul>';
        foreach ($categories as $cat) {
            $html .= '<li><a href="/category/' . $cat . '">' . ucfirst($cat) . '</a></li>';
        }
        $html .= '</ul>';
        $html .= '<a href="/">← Back to Home</a>';
        
        return $html;
    })
    ->whereAlpha('name')
    ->defaults(['name' => 'all'])
    ->name('category');

    // ============================================
    // COMPLEX PATTERNS
    // ============================================
    
    // Order by alphanumeric order ID
    $router->get('/orders/{orderId}', function ($request, $orderId) {
        return '<h1>Order Details</h1>
                <p>Order ID: <strong>' . htmlspecialchars($orderId) . '</strong></p>
                <p>Status: Delivered</p>
                <p>Total: $129.99</p>
                <a href="/">← Back to Home</a>';
    })
    ->whereAlphaNumeric('orderId')
    ->name('orders.show');

    // API versioning
    $router->get('/api/{version}/users', function ($request, $version) {
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];
        
        return [
            'api_version' => $version,
            'users' => $users,
            'count' => count($users)
        ];
    })
    ->where('version', 'v[1-9]')
    ->name('api.users');

    // UUID route example
    $router->get('/documents/{uuid}', function ($request, $uuid) {
        return '<h1>Document Viewer</h1>
                <p>Document UUID: <code>' . htmlspecialchars($uuid) . '</code></p>
                <p>✅ Valid UUID format!</p>
                <a href="/">← Back to Home</a>';
    })
    ->whereUuid('uuid')
    ->name('documents.show');

    // ============================================
    // STANDARD ROUTES
    // ============================================
    
    $router->get('/about', function ($request) {
        return '<h1>About Dew Framework</h1>
                <p>A modern PHP framework with advanced routing capabilities.</p>
                <a href="/">← Back to Home</a>';
    })->name('about');

    $router->get('/contact', function ($request) {
        return '<h1>Contact Us</h1>
                <p>Email: contact@dewframework.dev</p>
                <a href="/">← Back to Home</a>';
    })->name('contact');
};