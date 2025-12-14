<?php

declare(strict_types=1);

use Dew\MyFramework\Core\Application;
use Dew\MyFramework\Http\Request;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Capture the current HTTP request
$request = Request::capture();

// Create application
$app = new Application(BASE_PATH);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dew Framework - Request Testing</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        h1 { color: #667eea; margin-bottom: 10px; }
        h2 { color: #333; margin: 30px 0 15px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .info-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info-card p {
            color: #555;
            line-height: 1.6;
            word-break: break-all;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .badge.success { background: #28a745; }
        .badge.warning { background: #ffc107; color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover { background: #f8f9fa; }
        code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .test-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input[type="text"], input[type="email"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        input[type="text"]:focus, input[type="email"]:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Dew Framework v<?= $app->version() ?></h1>
        <p style="color: #666; margin-bottom: 30px;">HTTP Request Class Testing</p>

        <div class="info-grid">
            <div class="info-card">
                <h3>Request Method</h3>
                <p><span class="badge"><?= $request->method() ?></span></p>
            </div>
            <div class="info-card">
                <h3>Request URI</h3>
                <p><code><?= htmlspecialchars($request->uri()) ?></code></p>
            </div>
            <div class="info-card">
                <h3>Full URL</h3>
                <p style="font-size: 13px;"><code><?= htmlspecialchars($request->fullUrl()) ?></code></p>
            </div>
            <div class="info-card">
                <h3>Is Secure (HTTPS)</h3>
                <p><span class="badge <?= $request->isSecure() ? 'success' : 'warning' ?>">
                    <?= $request->isSecure() ? 'YES' : 'NO' ?>
                </span></p>
            </div>
            <div class="info-card">
                <h3>Client IP</h3>
                <p><code><?= htmlspecialchars($request->ip()) ?></code></p>
            </div>
            <div class="info-card">
                <h3>Is AJAX Request</h3>
                <p><span class="badge <?= $request->isAjax() ? 'success' : 'warning' ?>">
                    <?= $request->isAjax() ? 'YES' : 'NO' ?>
                </span></p>
            </div>
        </div>

        <h2>📝 Test Form - Submit Data</h2>
        <div class="test-form">
            <form method="POST" action="<?= $request->uri() ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($request->input('name', '')) ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($request->input('email', '')) ?>">
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4"><?= htmlspecialchars($request->input('message', '')) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="country">Country:</label>
                    <select id="country" name="country">
                        <option value="">Select...</option>
                        <option value="us" <?= $request->input('country') === 'us' ? 'selected' : '' ?>>United States</option>
                        <option value="uk" <?= $request->input('country') === 'uk' ? 'selected' : '' ?>>United Kingdom</option>
                        <option value="ca" <?= $request->input('country') === 'ca' ? 'selected' : '' ?>>Canada</option>
                    </select>
                </div>
                <button type="submit">Submit Form</button>
            </form>
        </div>

        <?php if ($request->isPost() && $request->has('name')): ?>
        <h2>✅ Form Data Received</h2>
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($request->only(['name', 'email', 'message', 'country']) as $key => $value): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($key) ?></strong></td>
                    <td><?= htmlspecialchars($value) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <h2>🔍 Request Headers</h2>
        <table>
            <thead>
                <tr>
                    <th>Header</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($request->headers() as $key => $value): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($key) ?></strong></td>
                    <td style="word-break: break-all;"><?= htmlspecialchars($value) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>⚙️ Request Methods Demo</h2>
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Result</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>$request->method()</code></td>
                    <td><span class="badge"><?= $request->method() ?></span></td>
                    <td>Get HTTP method</td>
                </tr>
                <tr>
                    <td><code>$request->isGet()</code></td>
                    <td><?= $request->isGet() ? '✅ true' : '❌ false' ?></td>
                    <td>Check if GET request</td>
                </tr>
                <tr>
                    <td><code>$request->isPost()</code></td>
                    <td><?= $request->isPost() ? '✅ true' : '❌ false' ?></td>
                    <td>Check if POST request</td>
                </tr>
                <tr>
                    <td><code>$request->has('name')</code></td>
                    <td><?= $request->has('name') ? '✅ true' : '❌ false' ?></td>
                    <td>Check if input exists</td>
                </tr>
                <tr>
                    <td><code>$request->userAgent()</code></td>
                    <td style="font-size: 12px; word-break: break-all;"><?= htmlspecialchars(substr($request->userAgent(), 0, 50)) ?>...</td>
                    <td>Get user agent</td>
                </tr>
            </tbody>
        </table>

        <h2>🧪 Try These URLs</h2>
        <ul style="line-height: 2; margin: 20px 0;">
            <li><a href="?test=hello">?test=hello</a> - Test query parameter</li>
            <li><a href="?name=John&email=john@example.com">?name=John&email=john@example.com</a> - Multiple parameters</li>
            <li><a href="/users/123">/users/123</a> - Test different path</li>
            <li><a href="/products?category=electronics">/products?category=electronics</a> - Path with query</li>
        </ul>

    </div>
</body>
</html>