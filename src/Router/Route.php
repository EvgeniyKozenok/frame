<?php

namespace John\Frame\Router;

/**
 * Class Route
 * @package John\Frame\Router
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
     * @var
     */
    private $checkMiddlewares;

    /**
     * Route constructor.
     * @param $name
     * @param $controller
     * @param $method
     * @param $params
     * @param $middlewares
     */
    public function __construct($name, $controller, $method, $params, $middlewares)
    {
        $this->name = $name;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = $params;
        $this->checkMiddlewares = $middlewares;
    }

    /**
     * @return mixed
     */
    public function getCheckMiddlewares()
    {
        return $this->checkMiddlewares;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }



}