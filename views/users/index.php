<?php
$pageContent = '<h1>' . htmlspecialchars($title) . '</h1>';
$pageContent .= '<p>List of all users in the system.</p>';
$pageContent .= '<table><thead><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead><tbody>';

foreach ($users as $user) {
    $pageContent .= '<tr>';
    $pageContent .= '<td>' . htmlspecialchars($user['id']) . '</td>';
    $pageContent .= '<td>' . htmlspecialchars($user['name']) . '</td>';
    $pageContent .= '<td><a href="/users/' . htmlspecialchars($user['id']) . '">View Profile</a></td>';
    $pageContent .= '</tr>';
}

$pageContent .= '</tbody></table>';
$pageContent .= '<p><a href="/" class="btn">← Back to Home</a></p>';

$content = $pageContent;
include __DIR__ . '/../layout.php';
