<?php

namespace John\Frame\Config;

class Config
{
    /**
     * @var array  Config storage
     */
    protected static $config = [];

    /**
     * Config constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        self::$config = array_merge(self::$config, $data);
    }

    /**
     * Load config
     *
     * @param $file
     */
    public function loadFromFile(string $file)
    {
        self::$config = include($file);
    }

    /**
     * Set config
     *
     * @param $data
     */
    public function set(string $data)
    {
        self::$config = $data;
    }

    /**
     * Get config param
     *
     * @param   string Param name
     *
     * @return mixed
     */
    public function __get(string $param_name)
    {
        return isset(self::$config[$param_name]) ? self::$config[$param_name] : null;
    }

    /**
     * Recursive getter
     *
     * @param Key|string $key Key may be complex like: db.host, db.driver, etc
     * @param array|bool $default
     * @return mixed
     */
    public function get(string $key = null, array $default = null): array
    {
        $chain = explode('.', $key);
        $node = self::$config;
        if (!empty($chain)) {
            do {
                $cell = array_shift($chain);
                if (!isset($node[$cell])) {
                    break;
                }
                $node = is_array($node) ? $node[$cell] : null;
            } while (!empty($chain) && !empty($node));
        }
        return $node ?? $default;
    }

    /**
     * Check if key exists
     *
     * @param Key|string $key Key may be complex like: db.host, db.driver, etc
     * @return bool
     */
    public function has(string $key): bool
    {
        $chain = explode('.', $key);
        $node = self::$config;
        do {
            $cell = array_shift($chain);
            if (!isset($node[$cell])) {
                return false;
            }
            $node = $node[$cell];
        } while (!empty($chain) && !empty($node));
        return true;
    }
}