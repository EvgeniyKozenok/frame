<?php

namespace john\frame;
use john\frame\Exceptions\Route\RouteException;
use john\frame\Logger\Logger;
use john\frame\Request\Request;
use john\frame\Router\Router;

/**
 * Class Application
 * @package john\frame
 */
class Application
{

    /**
     * config
     * log dir
     * @var array
     */
    private $config = [];
    private $log_dir = null;

    /**
     * Application constructor.
     * @param $config
     * @param $log_dir
     */
    public function __construct($config, $log_dir)
    {
        $this->config = $config;
        $this->log_dir = $log_dir;
    }

    /**
     * Application start
     */
    public function start()
    {
        if(!file_exists($this->log_dir) && !is_dir($this->log_dir))
            mkdir($this->log_dir);
        Logger::$PATH = $this->log_dir;
        $logger = Logger::getLogger('root', 'logger.log');
        $request = Request::getRequest();
        try {
            $this->debug($router = new Router($this->config));
            $this->debug($route = $router->getRoute($request));
            $this->debug($link = $router->getLink("get_one_good", ['name' => "test", 'id' => 10, 'test_param' => '123e']));
            $this->debug($request->getQueryParams('test', 'title', 'aaa'));
        } catch (RouteException $e) {
            $logger->log($e->getMessage());
        }
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