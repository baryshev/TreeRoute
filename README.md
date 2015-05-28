TreeRoute - request router
==========================

TreeRoute is a performance focused request router with regular expressions support.

Instalation
-----------

Install the latest version with `composer require baryshev/tree-route`

Usage
-----

```php
<?php

require __DIR__ . 'vendor/autoload.php';

$router = new \TreeRoute\Router();

// Defining route for several HTTP methods
$router->addRoute(['GET', 'POST'], '/', 'handler0');

// Defining route for one HTTP method
$router->addRoute('GET', '/news', 'handler1');

// Defining route with regular expression param
$router->get('/news/{id:^[0-9]+$}', 'handler2');

// Defining another route with regular expression param
$router->get('/news/{slug:^[a-zA-Z\-]+$}', 'handler3');

// Defining static route that conflicts with previous route, but static routes have high priority
$router->get('/news/all', 'handler4');

$method = 'GET';
$url = '/news/1';

$result = $router->dispatch($method, $url);

if (!isset($result['error'])) {
    $handler = $result['handler'];
    $params = $result['params'];
} else {
    switch ($result['error']['code']) {
        case 404 :
            // Not found handler here
            break;
        case 405 :
            // Method not allowed handler here
            $allowedMethods = $result['allowed'];
            if ($method == 'OPTIONS') {
                // OPTIONS method handler here
            }
            break;
    }
}
```