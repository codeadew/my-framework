<?php
$pageContent = <<<HTML
<h1>{$title}</h1>

<h2>User Details</h2>
<p><strong>ID:</strong> {$user['id']}</p>
<p><strong>Name:</strong> {$user['name']}</p>

<p>
    <a href="/users" class="btn">← Back to Users</a>
    <a href="/" class="btn">← Back to Home</a>
</p>
HTML;

$content = $pageContent;
include __DIR__ . '/../layout.php';