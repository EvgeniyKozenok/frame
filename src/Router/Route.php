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
     * uri for this route
     */
    private $uri;

    /**
     * Route constructor.
     * @param $name
     * @param $controller
     * @param $method
     * @param $params
     * @param $middlewares
     * @param $uri
     */
    public function __construct($name, $controller, $method, $params, $middlewares, $uri)
    {
        $this->name = $name;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = $params;
        $this->checkMiddlewares = $middlewares;
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return substr($this->uri,1, strlen($this->uri));
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