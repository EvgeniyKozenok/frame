<?php

namespace John\Frame\Middleware;

use John\Frame\Config\Config;
use John\Frame\DI\Injector;
use John\Frame\Exceptions\Middleware\MiddlewareException;

class Middleware
{
    protected $middlewareMaps = [];
    protected $checkMiddlewares = [];
    private $expectedTypes;

    public function __construct(Config $config, array $checks)
    {
        if ($checks) {
            $this->middlewareMaps = $config->get('middlewares');
            $this->checkMiddlewares = $checks;
            $injector = Injector::getInjector();
            $this->expectedTypes = $this->getKeys(MiddlewareI::class);
            $this->test($injector->get('Request'), $injector->get('response'));
        }
    }

    /**
     * Checking input middleware
     *
     * @param $request
     * @param $response
     * @return mixed
     * @throws MiddlewareException
     */
    private function test($request, $response)
    {
        $lastNext = function () use ($response) {
            return $response;
        };
        $previousNext = $lastNext;

        foreach (array_reverse($this->checkMiddlewares) as $middleName) {
            $middleParams = explode(':', $middleName);
            $middleName = array_shift($middleParams);
            $middleParams = explode(',', $middleParams[0]);
            if (array_key_exists($middleName, $this->middlewareMaps)) {
                if (!class_exists($this->middlewareMaps[$middleName])) {
                    throw new MiddlewareException("Class '" . $this->middlewareMaps[$middleName] . "' doesn't exist!");
                }
                $middleware = new $this->middlewareMaps[$middleName];
                if ($this->isValid($middleware)) {
                    if (explode(':', $this->checkMiddlewares[0])[0] === $middleName) {
                        return $middleware->handle($request, $previousNext, $middleParams);
                    } else {
                        $previousNext = function () use ($previousNext, $request, $middleware, $middleParams) {
                            return $middleware->handle($request, $previousNext, $middleParams);
                        };
                    }
                }
            }
        }
    }

    /**
     * Checking the validate of the expected type of parameters
     * with type of parameters the input object
     *
     * @param $obj
     * @return bool
     */
    private function isValid($obj): bool
    {
        return $this->expectedTypes === $this->getKeys($obj);
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
            array_push($paramsType, (string) $method->getReturnType());
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
}