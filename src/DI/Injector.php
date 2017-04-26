<?php

namespace John\Frame\DI;

use John\Frame\Config\Config;
use John\Frame\Exceptions\Injector\InjectorNotFoundClassException;
use John\Frame\Exceptions\Injector\InjectorNotFoundServiceException;
use John\Frame\Request\Request;

class Injector
{

    /**
     * @var array   Interface mapping
     */
    private $interface_mapping = [];

    /**
     * @var array   Config
     */
    private $config = [];

    /**
     * @var array   Service register
     */
    public $services = [];

    /**
     * Set config
     *
     * @param $cfg
     */
    public function __construct(Config $cfg){
        $this->config = $cfg;
        $this->interface_mapping = $cfg->get('services', []);
        print_r($this->interface_mapping);
    }

    /**
     * Set service
     *
     * @param String $serviceName
     * @param $service
     */
    public function set(String $serviceName, $service){
        $this->services[$serviceName] = $service;
    }

    /**
     *  Get service
     *
     * @param String $serviceName
     * @return mixed
     */
    public function get(String $serviceName)
    {
        if(!array_key_exists($serviceName, $this->services)) {
            $this->set($serviceName, $this->make($serviceName));
        }
        return $this->services[$serviceName];
    }

    /**
     * @param String $className
     * @return Request
     * @throws InjectorNotFoundClassException
     * @throws InjectorNotFoundServiceException
     * @internal param String $service
     */
    public function make(String $className)
    {
        if(!array_key_exists($className, $this->interface_mapping))
            throw new InjectorNotFoundServiceException("Add mapping in config file for '$className' service!");
        $className = $this->interface_mapping[$className];
        if(!class_exists($className))
            throw new InjectorNotFoundClassException(
                "Class '$className' for service '"
                .array_search($className, $this->interface_mapping)
                . "' not found in app!");
        try {
            $reflection = new \ReflectionClass($className);
            print_r($reflection);
            $reflectionClass = $reflection;
            $reflectionConstruct = $reflection->getConstructor();
            while(empty($reflectionConstruct) && !empty($reflection)){
                // Fallback to parent constructor
                $reflection = $reflection->getParentClass();
                $reflectionConstruct = $reflection ? $reflection->getConstructor() : null;
            }
            print_r($reflectionConstruct);


        } catch (\Exception $e) {}
        return Request::getRequest();
    }
}