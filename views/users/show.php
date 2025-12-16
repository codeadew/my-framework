<?php
$pageContent = '<h1>' . htmlspecialchars($title) . '</h1>';
$pageContent .= '<h2>User Details</h2>';
$pageContent .= '<p><strong>ID:</strong> ' . htmlspecialchars($user['id']) . '</p>';
$pageContent .= '<p><strong>Name:</strong> ' . htmlspecialchars($user['name']) . '</p>';
$pageContent .= '<p><a href="/users" class="btn">← Back to Users</a> ';
$pageContent .= '<a href="/" class="btn">← Back to Home</a></p>';

$content = $pageContent;
include __DIR__ . '/../layout.php';
