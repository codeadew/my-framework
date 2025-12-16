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
</ul>

<h2>🎯 Test Routes</h2>
<ul>
    <li><a href="/about">About Page</a></li>
    <li><a href="/users">Users List</a></li>
    <li><a href="/users/123">User Profile</a></li>
    <li><a href="/contact">Contact Form</a></li>
</ul>
HTML;

$content = $pageContent;
include __DIR__ . '/layout.php';
