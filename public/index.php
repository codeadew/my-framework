<?php

declare(strict_types=1);

use Dew\MyFramework\Core\Application;
use Dew\MyFramework\Http\Request;
use Dew\MyFramework\Http\Response;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Capture request
$request = Request::capture();
$app = new Application(BASE_PATH);

// Route the request based on path
$path = $request->path();

// Handle different response types based on URL
switch ($path) {
    case '/json':
        // JSON Response
        $data = [
            'success' => true,
            'message' => 'This is a JSON response',
            'timestamp' => time(),
            'data' => [
                'users' => ['Alice', 'Bob', 'Charlie'],
                'count' => 3
            ]
        ];
        $response = Response::json($data);
        break;

    case '/text':
        // Plain Text Response
        $response = Response::text('This is a plain text response from Dew Framework!');
        break;

    case '/redirect':
        // Redirect Response
        $response = Response::redirect('/', 302);
        break;

    case '/download':
        // File Download (we'll create a sample file)
        $sampleFile = BASE_PATH . '/storage/sample.txt';
        if (!file_exists($sampleFile)) {
            file_put_contents($sampleFile, "This is a sample downloadable file from Dew Framework!\nCreated at: " . date('Y-m-d H:i:s'));
        }
        $response = Response::download($sampleFile, 'framework-sample.txt');
        break;

    case '/404':
        // 404 Not Found
        $response = Response::notFound('The page you are looking for does not exist.');
        break;

    case '/500':
        // 500 Server Error
        $response = Response::serverError('Something went wrong on our end.');
        break;

    case '/403':
        // 403 Forbidden
        $response = Response::forbidden('You do not have permission to access this resource.');
        break;

    case '/401':
        // 401 Unauthorized
        $response = Response::unauthorized('Please authenticate to access this resource.');
        break;

    case '/api/users':
        // API endpoint with CORS
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com'],
        ];
        $response = Response::json(['users' => $users])->cors();
        break;

    case '/cookie':
        // Set a cookie
        $cookieHtml = '<!DOCTYPE html>
<html>
<head><title>Cookie Set</title></head>
<body style="font-family: Arial; padding: 50px; text-align: center;">
    <h1>Cookie Set!</h1>
    <p>A cookie named "framework_test" has been set.</p>
    <a href="/" style="color: #667eea;">Back to Home</a>
</body>
</html>';
        $response = Response::html($cookieHtml)->cookie('framework_test', 'Hello from Dew Framework', time() + 3600);
        break;

    default:
        // Default: Show response testing dashboard
        $response = Response::html(getDashboardHtml($request, $app));
        break;
}

// Send the response
$response->send();

/**
 * Generate dashboard HTML
 */
function getDashboardHtml($request, $app)
{
    $hasCookie = isset($_COOKIE['framework_test']);
    $cookieValue = isset($_COOKIE['framework_test']) ? $_COOKIE['framework_test'] : 'Not set';
    
    $version = $app->version();
    $method = $request->method();
    $uri = $request->uri();
    $cookieBadge = $hasCookie ? '' : 'warning';
    $cookieStatus = $hasCookie ? 'SET' : 'NOT SET';
    $cookieInfo = $hasCookie ? " - Value: <code>{$cookieValue}</code>" : '';
    
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dew Framework - Response Testing</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
        h2 {
            color: #333;
            margin: 30px 0 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .response-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .response-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .response-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
        .response-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .response-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.warning { background: #ffc107; color: #333; }
        code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: "Courier New", monospace;
            font-size: 13px;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box strong {
            color: #1976d2;
        }
        .code-block {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 20px 0;
        }
        .code-block pre {
            margin: 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Dew Framework v' . $version . '</h1>
        <p style="color: #666; margin-bottom: 30px;">HTTP Response Class Testing Dashboard</p>

        <div class="info-box">
            <strong>Current Request:</strong> <code>' . $method . ' ' . htmlspecialchars($uri) . '</code><br>
            <strong>Cookie Status:</strong> 
            <span class="badge ' . $cookieBadge . '">' . $cookieStatus . '</span>
            ' . $cookieInfo . '
        </div>

        <h2>📝 Response Types</h2>
        <div class="response-grid">
            <div class="response-card">
                <h3>HTML Response</h3>
                <p>The default response type. Returns formatted HTML content to the browser.</p>
                <code>Response::html($content)</code>
                <br><br>
                <span class="badge">Currently Viewing</span>
            </div>

            <div class="response-card">
                <h3>JSON Response</h3>
                <p>Perfect for APIs. Returns data in JSON format with proper headers.</p>
                <code>Response::json($data)</code>
                <br><br>
                <a href="/json" class="btn">Try JSON →</a>
            </div>

            <div class="response-card">
                <h3>Plain Text</h3>
                <p>Returns plain text without HTML formatting. Useful for simple data.</p>
                <code>Response::text($text)</code>
                <br><br>
                <a href="/text" class="btn">Try Text →</a>
            </div>

            <div class="response-card">
                <h3>Redirect</h3>
                <p>Redirects the user to another URL with proper HTTP status codes.</p>
                <code>Response::redirect($url)</code>
                <br><br>
                <a href="/redirect" class="btn">Try Redirect →</a>
            </div>

            <div class="response-card">
                <h3>File Download</h3>
                <p>Triggers a file download in the browser with proper headers.</p>
                <code>Response::download($path)</code>
                <br><br>
                <a href="/download" class="btn">Try Download →</a>
            </div>

            <div class="response-card">
                <h3>Set Cookie</h3>
                <p>Sets a cookie in the browser that persists across requests.</p>
                <code>Response::cookie($name, $value)</code>
                <br><br>
                <a href="/cookie" class="btn">Set Cookie →</a>
            </div>
        </div>

        <h2>⚠️ Error Responses</h2>
        <div class="response-grid">
            <div class="response-card">
                <h3>404 Not Found</h3>
                <p>Page or resource does not exist.</p>
                <code>Response::notFound()</code>
                <br><br>
                <a href="/404" class="btn">View 404 →</a>
            </div>

            <div class="response-card">
                <h3>401 Unauthorized</h3>
                <p>Authentication required.</p>
                <code>Response::unauthorized()</code>
                <br><br>
                <a href="/401" class="btn">View 401 →</a>
            </div>

            <div class="response-card">
                <h3>403 Forbidden</h3>
                <p>Access denied even with authentication.</p>
                <code>Response::forbidden()</code>
                <br><br>
                <a href="/403" class="btn">View 403 →</a>
            </div>

            <div class="response-card">
                <h3>500 Server Error</h3>
                <p>Something went wrong on the server.</p>
                <code>Response::serverError()</code>
                <br><br>
                <a href="/500" class="btn">View 500 →</a>
            </div>
        </div>

        <h2>🔧 Advanced Features</h2>
        <div class="response-grid">
            <div class="response-card">
                <h3>API with CORS</h3>
                <p>JSON response with Cross-Origin Resource Sharing headers enabled.</p>
                <code>Response::json()->cors()</code>
                <br><br>
                <a href="/api/users" class="btn">Try API →</a>
            </div>

            <div class="response-card">
                <h3>Method Chaining</h3>
                <p>Chain multiple methods together for complex responses.</p>
                <code>Response::json($data)<br>->cors()->noCache()</code>
            </div>

            <div class="response-card">
                <h3>Custom Headers</h3>
                <p>Set any custom HTTP headers on your response.</p>
                <code>->setHeader("X-Custom", "value")</code>
            </div>

            <div class="response-card">
                <h3>Status Codes</h3>
                <p>Full support for all HTTP status codes (200, 201, 301, 404, etc.)</p>
                <code>->setStatusCode(201)</code>
            </div>
        </div>

        <h2>💡 Code Examples</h2>
        <div class="code-block">
            <pre><code>// Simple HTML response
$response = Response::html("&lt;h1&gt;Hello World&lt;/h1&gt;");

// JSON API response
$response = Response::json(["status" => "success", "data" => $users]);

// Redirect with flash message
$response = Response::redirect("/dashboard");

// File download
$response = Response::download("/path/to/file.pdf");

// Method chaining
$response = Response::json($data)
    ->cors()
    ->noCache()
    ->setHeader("X-API-Version", "1.0");

// Send response
$response->send();</code></pre>
        </div>

    </div>
</body>
</html>';

    return $html;
}