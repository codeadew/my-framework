<?php

declare(strict_types=1);

namespace Dew\MyFramework\Controllers;

use Dew\MyFramework\Core\Controller;
use Dew\MyFramework\Http\Response;

/**
 * ApiController
 * 
 * Base controller for API endpoints
 */
class ApiController extends Controller
{
    /**
     * Return success response
     */
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): Response
    {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return error response
     */
    protected function error(string $message, int $status = 400, array $errors = []): Response
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $this->json($response, $status);
    }

    /**
     * Get API status
     */
    public function status(): Response
    {
        return $this->success([
            'framework' => 'Dew Framework',
            'version' => '1.0.0-dev',
            'timestamp' => time(),
            'uptime' => $this->getUptime(),
        ]);
    }

    /**
     * Get server uptime (simplified)
     */
    protected function getUptime(): string
    {
        $uptime = time() - $_SERVER['REQUEST_TIME'];
        return gmdate('H:i:s', $uptime);
    }
}