<?php
$pageContent = '<h1>' . htmlspecialchars($title) . '</h1>';
$pageContent .= '<p>Get in touch with us.</p>';
$pageContent .= '<p><a href="/" class="btn">← Back to Home</a></p>';

$content = $pageContent;
include __DIR__ . '/layout.php';
