<?php
$pageContent = <<<HTML
<h1>{$title}</h1>
<p>A modern PHP framework built from scratch for learning and understanding how frameworks work.</p>

<h2>Features</h2>
<ul>
HTML;

foreach ($features as $feature) {
    $pageContent .= "<li>{$feature}</li>\n";
}

$pageContent .= <<<HTML
</ul>

<p><a href="/" class="btn">← Back to Home</a></p>
HTML;

$content = $pageContent;
include __DIR__ . '/layout.php';