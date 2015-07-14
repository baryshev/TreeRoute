<?php

namespace TreeRoute;

/**
 * This model represents an error generated while attempting to dispatch a Router.
 *
 * @see Router::dispatch()
 */
class Error extends Result
{
    /**
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @var int error code
     */
    public $code;

    /**
     * @var string error message
     */
    public $message;

    /**
     * @var string[] list of allowed HTTP methods
     */
    public $allowed = array();
}
