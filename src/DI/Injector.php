<?php

namespace John\Frame\DI;

use John\Frame\Config\Config;
use John\Frame\Exceptions\Injector\InjectorNotFoundClassException;
use John\Frame\Exceptions\Injector\InjectorNotFoundServiceException;

class Injector
{
    const SCALAR_TYPES = ['int', 'bool', 'string', 'float', 'array'];

    /**
     * instance of injector
     *
     * @var null
     */
    public static $injector = null;

    /**
     * @var array   Interface mapping
     */
    private $interface_mapping = [];

    /**
     * @var array   Config
     */
    private $config = [];

    /**
     * Service register if it singleton
     *
     * @var array
     */
    public static $instances = [];

    /**
     * @var array Service register if it some object
     */
    public static $services = [];

    /**
     * Set config
     *
     * @param $cfg
     */
    private function __construct(Config $cfg)
    {
        $this->config = $cfg;
        $this->interface_mapping = array_change_key_case($cfg->get('services', []));
    }


    /**
     * @param Config|null $config
     * @return Injector
     */
    public static function getInjector(Config $config = null): self
    {
        if (!self::$injector)
            self::$injector = new self($config);
        return self::$injector;
    }

    /**
     * Set service
     *
     * @param String $serviceName
     * @param $service
     */
    public function set(String $serviceName, $service)
    {
        self::$services[strtolower($serviceName)] = $service;
    }

    /**
     *  Get service
     *
     * @param String $serviceName
     * @return mixed
     */
    public function get(String $serviceName)
    {
        $serviceName = strtolower($serviceName);
        if (!array_key_exists($serviceName, self::$services) &&
            !array_key_exists($serviceName, self::$instances)) {
            $this->set($serviceName, $this->make($serviceName));
        }
        return self::$services[$serviceName];
    }

    /**
     * create service
     *
     * @param String $className
     * @param array $actualParams
     * @throws InjectorNotFoundClassException
     * @throws InjectorNotFoundServiceException
     * @throws \Exception
     * @internal param String $service
     * @return null|object
     */
    public function make(string $className, $actualParams = [])
    {
        if (!array_key_exists($className, $this->interface_mapping)) {
            throw new InjectorNotFoundServiceException(
                "Add mapping in config file for '$className' service!");
        }
        $className = $this->interface_mapping[$className];
        if (!class_exists($className)) {
            throw new InjectorNotFoundClassException(
                "Class '$className' for service '"
                . array_search($className, $this->interface_mapping)
                . "' not found in app!");
        }
        try {
            $reflectionClass = new \ReflectionClass($className);
            $nextReflectionClass = $reflectionClass;
            $reflectionConstruct = $reflectionClass->getConstructor();
            while ($nextReflectionClass) {
                $nextReflectionClass = $reflectionClass->getParentClass();
                $nameClass = strtolower(array_pop(explode('\\', $nextReflectionClass->name)));
                if ($nameClass) {
                    $this->interface_mapping[$nameClass] = $nextReflectionClass->name;
                    $reflectionClass = $nextReflectionClass;
                    $this->get($nameClass);
                }
            }
            $instance = null;
            if ($reflectionConstruct) {
                $reflectionConstructParams = $reflectionConstruct->getParameters();
            }
            $paramsSet = [];
            if (!empty($reflectionConstructParams)) {
                foreach ($reflectionConstructParams as $param) {
                    $name = $param->getName();
                    $type = '';
                    if ($param->hasType()) {
                        $type = $param->getType();
                    }
                    $default = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                    $paramsSet = array_merge($paramsSet, $this->resolveParams(array_merge($this->lookupConfigParams($this->getClassSlug($className)), $actualParams),
                        $name, $type, $default));
                }
            }
            if( $reflectionClass->hasMethod('getInstance') ) {
                $instance = call_user_func_array([$className, 'getInstance'], $paramsSet);
                self::$instances[array_search($className, $this->interface_mapping)] = $instance;
            } else if ( $reflectionClass->hasMethod('get'. array_pop(explode("\\", $reflectionClass->name)))){
                $instance = call_user_func_array([$className, 'get'. array_pop(explode("\\", $reflectionClass->name))], $paramsSet);
                self::$instances[array_search($className, $this->interface_mapping)] = $instance;
            } else {
                $instance = $reflectionClass->newInstanceArgs($paramsSet);
            }
            return $instance;
        } catch (\Exception $e) {
            throw new \Exception('Unable to resolve class '. $className . ': ' . $e->getMessage());
        }
    }

    /**
     * Get class slug
     *
     * @param string $className
     * @return array|string
     *
     */
    private function lookupConfigParams(string $className): array
    {
        return $this->config->has($className) ? (array)$this->config->get($className) : [];
    }

    /**
     * Return service params in config
     *
     * @param string $className
     * @return array|string
     *
     */
    private function getClassSlug(string $className): string
    {
        return strtolower(array_pop(explode("\\", $className)));
    }

    /**
     * Resolve params required by class constructor
     *
     * @param array $actualParams
     * @param string $name
     * @param string $type
     * @param $default
     * @return array
     * @internal param $isDefault
     * @internal param array $requestedParams
     */
    private function resolveParams(array $actualParams = [], string $name,
                                   string $type, $default): array
    {
        $params[$name] = '';
        if ($type) {
            if (!in_array($type, self::SCALAR_TYPES)) {
                $params[$name] = $this->make(array_pop(explode("\\", $type)));
            } else {
                $params[$name] = $this->isDef($name, $actualParams, $default);
            }
        } else {
            $params[$name] = $this->isDef($name, $actualParams, $default);
        }
        return $params;
    }

    /**
     * Help function to constructor resolve params
     * @param $name
     * @param $actualParams
     * @param $default
     * @return mixed|string
     * @throws \Exception
     * @internal param $bool
     */
    private function isDef($name, $actualParams, $default)
    {
        if (array_key_exists($name, $actualParams)) {
            return $actualParams[$name];
        }
        if ($default || $default === '') {
            return $default;
        }
        throw new \Exception(sprintf('Unable to find value param [%s]', $name));
    }

}