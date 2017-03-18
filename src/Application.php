<?php

namespace john\frame;
use john\frame\Exceptions\Config\ConfigException;
use john\frame\Exceptions\Route\RouteException;
use john\frame\Logger\Logger;
use john\frame\Request\Request;
use john\frame\Response\Response;
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
//    private $log_dir = null;
    private $logger = '';

    /**
     * Application constructor.
     * @param $config
     * @param $log_dir
     */
    public function __construct($config, $log_dir)
    {
        echo $_SERVER['DOCUMENT_ROOT'];
        $this->log_dir = $log_dir;
        file_exists($this->log_dir) && is_dir($this->log_dir) ? : mkdir($this->log_dir);
        Logger::$PATH = $log_dir;
        $this->logger = Logger::getLogger('root', 'logger.log');
        $this->config = $config;
        if (!is_array($this->config)) {
            $message = "Routes config not found!";
            $this->logger->log($message);
            die($message);
        }
    }

    /**
     * Application start
     */
    public function start()
    {
        $request = Request::getRequest();
        try {
            $router = new Router($this->config);
            $route = $router->getRoute($request);
            $route_controller = $route->getController();
            $route_method = $route->getMethod();
            if (class_exists($route_controller)) {
                $reflectionClass = new \ReflectionClass($route_controller);
                if ($reflectionClass->hasMethod($route_method)) {
                    $controller = $reflectionClass->newInstance();
                    $reflectionMethod = $reflectionClass->getMethod($route_method);
                    $response = $reflectionMethod->invokeArgs($controller, $route->getParams());
                    if ($response instanceof Response) {
                        $response->send();
                    }
                }
            }
        } catch (RouteException $e) {
            echo $e->getMessage();
            $this->logger->log($e->getMessage());
        } catch (ConfigException $e) {
            echo $e->getMessage();
            $this->logger->log($e->getMessage());
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->logger->log($e->getMessage());
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