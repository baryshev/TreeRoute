TreeRoute - request router
==========================

TreeRoute is a performance focused request router with regular expressions support.

Installation
-----------

Install the latest version with `composer require baryshev/tree-route`

Usage
-----

Basic usage:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$router = new \TreeRoute\Router();

// Defining route for several HTTP methods
$router->addRoute(['GET', 'POST'], '/', 'handler0');

// Defining route for one HTTP method
$router->addRoute('GET', '/news', 'handler1');

// Defining route with regular expression param
$router->get('/news/{id:[0-9]+}', 'handler2');

// Defining another route with regular expression param
$router->get('/news/{slug:[a-zA-Z\-]+}', 'handler3');

// Defining static route that conflicts with previous route, but static routes have high priority
$router->get('/news/all', 'handler4');

// Defining another route
$router->post('/news', 'handler5');

$method = 'GET';

// Optionally pass HEAD requests to GET handlers
// if ($method == 'HEAD') {
//    $method = 'GET';
// }

$url = '/news/1';

$result = $router->dispatch($method, $url);

if (!isset($result['error'])) {
    $handler = $result['handler'];
    $params = $result['params'];
    // Do something with handler and params
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

Save and restore routes (useful for routes caching):

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$router = new \TreeRoute\Router();
$router->addRoute(['GET', 'POST'], '/', 'handler0');
$router->addRoute('GET', '/news', 'handler1');

$routes = $router->getRoutes();

$anotherRouter = new \TreeRoute\Router();
$anotherRouter->setRoutes($routes);

$method = 'GET';
$url = '/news';

$result = $anotherRouter->dispatch($method, $url);
```

Testing
---------

```bash
composer install
./vendor/bin/codecept run
```

Benchmark
---------

https://github.com/baryshev/FastRoute-vs-TreeRoute
