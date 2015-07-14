<?php

namespace TreeRoute;

/**
 * This class represents a route, or part of a route, within a Router.
 */
class Route
{
    /**
     * @var string route pattern
     */
    public $route;

    /**
     * @var callable[] map where HTTP method => callable method handler
     */
    public $methods = array();

    /**
     * @var string|null parameter name (or NULL, if this route does not match a parameter)
     */
    public $name;

    /**
     * @var Route[] list of nested Route instances
     */
    public $childs = array();

    /**
     * @var Route[] map where regular expression => nested Route instance
     */
    public $regexps = array();

    /**
     * @var Route root of other possible routes
     */
    public $others;
}
