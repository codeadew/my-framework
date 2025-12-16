<?php

declare(strict_types=1);

use Dew\MyFramework\Routing\Router;

return function (Router $router) {
    
    // Home routes (using HomeController)
    $router->get('/', 'Dew\MyFramework\Controllers\HomeController@index')
           ->name('home');
    
    $router->get('/about', 'Dew\MyFramework\Controllers\HomeController@about')
           ->name('about');
    
    $router->get('/contact', 'Dew\MyFramework\Controllers\HomeController@contact')
           ->name('contact');
    
    $router->post('/contact', 'Dew\MyFramework\Controllers\HomeController@submitContact')
           ->name('contact.submit');

    // User routes (using UserController)
    $router->get('/users', 'Dew\MyFramework\Controllers\UserController@index')
           ->name('users.index');
    
    $router->get('/users/{id}', 'Dew\MyFramework\Controllers\UserController@show')
           ->whereNumber('id')
           ->name('users.show');
    
    $router->get('/users/create', 'Dew\MyFramework\Controllers\UserController@create')
           ->name('users.create');
    
    $router->post('/users', 'Dew\MyFramework\Controllers\UserController@store')
           ->name('users.store');

    // API routes (using ApiController)
    $router->get('/api/status', 'Dew\MyFramework\Controllers\ApiController@status')
           ->name('api.status');
};



├── src/
│   ├── Controllers/
│   │   ├── HomeController.php           ← NEW
│   │   ├── UserController.php           ← NEW
│   │   └── ApiController.php            ← NEW
│   ├── Core/
│   │   ├── Application.php              ← UPDATED
│   │   ├── Controller.php               ← NEW
│   │   ├── Container.php
│   │   └── ...
│   ├── Http/
│   └── Routing/
│       └── Router.php                   ← UPDATED
├── views/
│   ├── layout.php                       ← NEW
│   ├── home.php                         ← NEW
│   ├── about.php                        ← NEW
│   ├── contact.php                      ← NEW
│   └── users/
│       ├── index.php                    ← NEW
│       ├── show.php                     ← NEW
│       └── create.php                   ← NEW
└── vendor/