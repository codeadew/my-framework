<?php
$pageContent = <<<HTML
<h1>{$title}</h1>
<p>List of all users in the system.</p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
HTML;

foreach ($users as $user) {
    $pageContent .= "<tr>
        <td>{$user['id']}</td>
        <td>{$user['name']}</td>
        <td><a href=\"/users/{$user['id']}\">View Profile</a></td>
    </tr>\n";
}

$pageContent .= <<<HTML
    </tbody>
</table>

<p><a href="/" class="btn">← Back to Home</a></p>
HTML;

$content = $pageContent;
include __DIR__ . '/../layout.php';