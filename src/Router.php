<?php

namespace TreeRoute;

class Router
{
    const PARAM_REGEXP = '/^{((([^:]+):(.+))|(.+))}$/';
    const SEPARATOR_REGEXP = '/^[\s\/]+|[\s\/]+$/';

    /**
     * @var Route
     */
    private $routes;

    public function __construct()
    {
        $this->routes = new Route();
    }

    /**
     * @param string $url
     *
     * @return Match|null match information (or NULL, if no match was found)
     */
    private function match($url)
    {
        $parts = explode('?', $url, 1);
        $parts = explode('/', preg_replace(self::SEPARATOR_REGEXP, '', $parts[0]));
        if (sizeof($parts) === 1 && $parts[0] === '') {
            $parts = [];
        }
        $params = [];
        $current = $this->routes;

        for ($i = 0, $length = sizeof($parts); $i < $length; $i++) {
            if (isset($current->childs[$parts[$i]])) {
                $current = $current->childs[$parts[$i]];
            } else {
                foreach ($current->regexps as $regexp => $route) {
                    if (preg_match('/' . addcslashes($regexp, '/') . '/', $parts[$i])) {
                        $current = $route;
                        $params[$current->name] = $parts[$i];
                        continue 2;
                    }
                }
                if ($current->others) {
                    $current = $current->others;
                    $params[$current->name] = $parts[$i];
                } else {
                    return null;
                }
            }
        }

        if (!isset($current->methods)) {
            return null;
        } else {
            return new Match(
                $current->route,
                $current->methods,
                $params
            );
        }
    }

    /**
     * @param string $name
     *
     * @return Route
     */
    private function createRouteWithName($name)
    {
        $route = new Route();
        $route->name = $name;
        return $route;
    }

    /**
     * @param string|string[] $methods HTTP request method (or list of methods)
     * @param string $route
     * @param $handler
     *
     * @return void
     */
    public function addRoute($methods, $route, $handler)
    {
        $methods = (array) $methods;

        $parts = explode('?', $route, 1);
        $parts = explode('/', preg_replace(self::SEPARATOR_REGEXP, '', $parts[0]));
        if (sizeof($parts) === 1 && $parts[0] === '') {
            $parts = [];
        }

        $current = $this->routes;
        foreach ($parts as $part) {
            $paramsMatch = preg_match(self::PARAM_REGEXP, $part, $paramsMatches);
            if ($paramsMatch) {
                if (!empty($paramsMatches[2])) {
                    if (!isset($current->regexps[$paramsMatches[4]])) {
                        $current->regexps[$paramsMatches[4]] = $this->createRouteWithName($paramsMatches[3]);
                    }
                    $current = $current->regexps[$paramsMatches[4]];
                } else {
                    if ($current->others === null) {
                        $current->others = $this->createRouteWithName($paramsMatches[5]);
                    }
                    $current = $current->others;
                }
            } else {
                if (!isset($current->childs[$part])) {
                    $current->childs[$part] = new Route();
                }
                $current = $current->childs[$part];
            }
        }

        $current->route = $route;
        foreach ($methods as $method) {
            $current->methods[strtoupper($method)] = $handler;
        }
    }

    /**
     * @param string $url
     *
     * @return string[]|null
     */
    public function getOptions($url)
    {
        $route = $this->match($url);
        if (!$route) {
            return null;
        } else {
            return array_keys($route->methods);
        }
    }

    /**
     * @param string $method HTTP method name
     * @param string $url
     *
     * @return Result
     */
    public function dispatch($method, $url)
    {
        $match = $this->match($url);

        $result = new Result();
        $result->url = $url;
        $result->method = $method;

        if (!$match) {
            $result->error = new Error(404, 'Not Found');
        } else {
            $result->route = $match->route;
            $result->params = $match->params;

            if (isset($match->methods[$method])) {
                $result->handler = $match->methods[$method];
            } else {
                $result->error = new Error(405, 'Method Not Allowed');
                $result->error->allowed = array_keys($match->methods);
            }
        }

        return $result;
    }

    /**
     * @return Route
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param Route $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function options($route, $handler)
    {
        $this->addRoute('OPTIONS', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function get($route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function head($route, $handler)
    {
        $this->addRoute('HEAD', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function post($route, $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function put($route, $handler)
    {
        $this->addRoute('PUT', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function delete($route, $handler)
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function trace($route, $handler)
    {
        $this->addRoute('TRACE', $route, $handler);
    }

    /**
     * @param string $route
     * @param callable $handler
     *
     * @return void
     */
    public function connect($route, $handler)
    {
        $this->addRoute('CONNECT', $route, $handler);
    }
}
