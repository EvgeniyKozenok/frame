<?php

namespace john\frame\Router;

/**
 * Class Route
 * @package john\frame\Router
 */
class Route
{
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $controller;
    /**
     * @var
     */
    private $method;
    /**
     * @var
     */
    private $params;

    /**
     * Route constructor.
     * @param $name
     * @param $controller
     * @param $method
     * @param $params
     */
    public function __construct($name, $controller, $method, $params)
    {
        $this->name = $name;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = $params;
        echo "You route: <hr />";
    }

}