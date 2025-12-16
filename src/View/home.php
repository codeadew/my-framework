<?php
$pageContent = <<<HTML
<h1>🎉 {$title}</h1>
<p>Version: <strong>{$version}</strong></p>

<h2>Welcome to Controller-based Architecture!</h2>
<p>This page is now rendered by the <code>HomeController</code> class.</p>

<h2>✨ What's New</h2>
<ul>
    <li>Base Controller class with common functionality</li>
    <li>Controller method routing (Controller@method)</li>
    <li>Simple view rendering system</li>
    <li>Built-in validation helpers</li>
    <li>Automatic dependency injection in controllers</li>
</ul>

<h2>🎯 Test Routes</h2>
<ul>
    <li><a href="/about">About Page</a> - HomeController@about</li>
    <li><a href="/users">Users List</a> - UserController@index</li>
    <li><a href="/users/123">User Profile</a> - UserController@show</li>
    <li><a href="/contact">Contact Form</a> - HomeController@contact</li>
    <li><a href="/api/status">API Status</a> - ApiController@status</li>
</ul>
HTML;

$content = $pageContent;
include __DIR__ . '/layout.php';