<?php
$pageContent = '<h1>' . htmlspecialchars($title) . '</h1>';
$pageContent .= '<p>A modern PHP framework built from scratch.</p>';
$pageContent .= '<h2>Features</h2><ul>';

foreach ($features as $feature) {
    $pageContent .= '<li>' . htmlspecialchars($feature) . '</li>';
}

$pageContent .= '</ul><p><a href="/" class="btn">← Back to Home</a></p>';

$content = $pageContent;
include __DIR__ . '/layout.php';
