<?php

namespace John\Frame\Middleware;

use John\Frame\Config\Config;
use John\Frame\Controller\BaseController;
use John\Frame\DI\Injector;
use John\Frame\Exceptions\Middleware\MiddlewareException;


class Middleware
{
    private $middlewareMaps = [];
    private $checkMiddlewares = [];
    private $expectedTypes;
    private $response;

    /**
     * Middleware constructor.
     * @param Config $config
     * @param Injector $injector
     * @param BaseController $controller
     * @param \ReflectionMethod $method
     * @param array $params
     * @param array $checks
     */
    public function __construct(Config $config,
                                Injector $injector,
                                BaseController $controller,
                                \ReflectionMethod $method,
                                array $params,
                                array $checks)
    {
        $this->middlewareMaps = $config->__get('middlewares');
        $this->checkMiddlewares = $checks;
        $this->expectedTypes = $this->getKeys(MiddlewareI::class);
        $this->test($controller, $method, $params, $injector);
    }

    /**
     * Checking input middleware
     *
     * @param $controller
     * @param $method
     * @param $params
     * @param $ing
     * @return mixed
     * @throws MiddlewareException
     * @internal param $request
     * @internal param $response
     */
    private function test($controller, $method, $params, $ing)
    {
        $lastNext = function () use ($controller, $method, $params) {
            return $method->invokeArgs($controller, $params);
        };
        $previousNext = $lastNext;
        foreach (array_reverse($this->checkMiddlewares) as $middleName) {
            $middleParams = explode(':', array_shift($this->checkMiddlewares));
            $middleName = array_shift($middleParams);
            if ($middleParams) {
                $middleParams = explode(',', array_shift($middleParams));
            }
            if (array_key_exists($middleName, $this->middlewareMaps)) {
                if (!class_exists($this->middlewareMaps[$middleName])) {
                    throw new MiddlewareException("Class '" . $this->middlewareMaps[$middleName] . "' doesn't exist!");
                }
                $middleware = new $this->middlewareMaps[$middleName];
                $wrongParameters = $this->isValid($middleware);
                if (!$wrongParameters) {
                    if (next($this->checkMiddlewares)) {
                        $previousNext = function () use ($previousNext, $ing, $middleware, $middleParams, $middleName) {
                            $response = $middleware->handle($ing->get('request'), $previousNext, $middleParams);
                            return $response;
                        };
                    } else {
                        $this->response = $middleware->handle($ing->get('request'), $previousNext, $middleParams);
                    }
                } else {
                    throw new MiddlewareException("Wrong in '" . get_class($middleware) . "': $wrongParameters");
                }
            } else {
                throw new MiddlewareException("Not found middleware class for alias name '" . $middleName . "'!");
            }
        }
    }

    /**
     * Checking the validate of the expected type of parameters
     * with type of parameters the input object
     *
     * @param $obj
     * @return string
     */
    private function isValid($obj): string
    {
        $result = '';
        $actualTypes = $this->getKeys($obj);
        $returnValue = array_shift($this->expectedTypes);
        if (array_shift($actualTypes) != $returnValue) {
            $result .= "Return value of middleware must be as '" . $returnValue . "'.";
        }
        for ($i = 0; $i < count($this->expectedTypes); $i++) {
            if($this->expectedTypes[$i] != $actualTypes[$i]) {
                $result .=  " " . ($i+1) . " middleware parameter must be '" . $this->expectedTypes[$i] . "' type.";
            }
        }
        array_unshift($this->expectedTypes, $returnValue);
        return $result;
    }

    /**
     * Get types parameters of input function
     *
     * @param $class
     * @return array
     * @throws MiddlewareException
     */
    private function getKeys($class): array
    {
        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->hasMethod('handle')) {
            throw new MiddlewareException("Method 'handle' does not exist in class '" . $reflectionClass->getName() . "'!");
        }
        $method = $reflectionClass->getMethod('handle');
        $paramsType = [];
        if ($method->hasReturnType())
            array_push($paramsType, (string)$method->getReturnType());
        $params = $method->getParameters();
        foreach ($params as $param) {
            if ($param->hasType()) {
                array_push($paramsType, (string)$param->getType());
            } else {
                array_push($paramsType, null);
            }
        }
        return $paramsType;
    }

    /**
     * return Response object
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}