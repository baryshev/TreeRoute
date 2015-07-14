<?php

namespace TreeRoute;

/**
 * This model represents the result of an attempted match.
 *
 * @see Router::match()
 */
class Match
{
    /**
     * @param string     $route
     * @param callable[] $methods
     * @param string[]   $params
     */
    public function __construct($route, $methods, $params)
    {
        $this->route = $route;
        $this->methods = $methods;
        $this->params = $params;
    }

    /**
     * @var string the route expression that was matched
     */
    public $route;

    /**
     * @var callable[] map where HTTP method name => callable
     */
    public $methods;

    /**
     * @var string[] map where parameter name => parameter
     */
    public $params;
}
