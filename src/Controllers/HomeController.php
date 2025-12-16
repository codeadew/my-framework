<?php

declare(strict_types=1);

namespace Dew\MyFramework\Controllers;

use Dew\MyFramework\Core\Controller;
use Dew\MyFramework\Http\Response;

/**
 * HomeController
 * 
 * Handles home page and general pages
 */
class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index(): Response
    {
        return $this->view('home', [
            'title' => 'Welcome to Dew Framework',
            'version' => '1.0.0-dev',
        ]);
    }

    /**
     * Display the about page
     */
    public function about(): Response
    {
        $features = [
            'Advanced Routing',
            'Dependency Injection',
            'Middleware System',
            'Controller Architecture',
            'Request/Response Handling',
        ];

        return $this->view('about', [
            'title' => 'About Dew Framework',
            'features' => $features,
        ]);
    }

    /**
     * Display contact page
     */
    public function contact(): Response
    {
        return $this->view('contact', [
            'title' => 'Contact Us',
        ]);
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(): Response
    {
        try {
            // Validate input
            $data = $this->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'message' => 'required|min:10',
            ]);

            // In a real app, you'd save to database or send email
            // For now, just return success

            return $this->json([
                'success' => true,
                'message' => 'Thank you for your message!',
                'data' => $data,
            ]);

        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => json_decode($e->getMessage(), true),
            ], 422);
        }
    }
}