<?php

namespace John\Frame\Service;

/**
 * Storage and use of application services
 *
 * Class ServiceContainer
 * @package John\Frame\Service
 */
class ServiceContainer
{
    /**
     * @var array jf services
     */
    private $services = [];

    private static $service = null;

    /**
     * ServiceContainer constructor.
     */
    private function __construct()
    {
    }


    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * Returns service container
     *
     * @return ServiceContainer
     */
    public static function getService(): self
    {
        if (!self::$service)
            self::$service = new self();
        return self::$service;
    }

    /**
     * Getting a specific application service
     *
     * @param $key
     * @return array|object
     */
    public function getServices(string $key)
    {
        return $this->services[$key];
    }

    /**
     * Added new application service
     *
     * @param string $key
     * @param object $service
     * @internal param array $services
     */
    public function setServices(string $key, $service)
    {
        $this->services[$key] = $service;
    }
}