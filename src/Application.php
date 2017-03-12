<?php

namespace john\frame;

/**
 * Class Application
 * @package john\frame
 */
class Application
{

    /**
     * @var array
     */
    private $config = [];

    /**
     * Application constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Application start
     */
    public function start()
    {
        $this->debug($this->config);
    }

    /**
     * Application __destruct
     */
    public function __destruct()
    {
        // TODO
    }

    /**
     * Application help function debug
     * @param $o - object
     */
    private function debug($o)
    {
        echo "<pre>";
        print_r($o);
        echo "</pre>";
    }

}