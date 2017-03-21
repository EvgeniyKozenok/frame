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
    private static $services = [];

    /**
     * Getting a specific application service
     *
     * @param $key
     * @return array|object
     */
    public static function getServices($key)
    {
        return self::$services[$key];
    }

    /**
     * Added new application service
     *
     * @param string $key
     * @param object $service
     * @internal param array $services
     */
    public static function setServices(string $key, $service)
    {
        self::$services[$key] = $service;
    }
}