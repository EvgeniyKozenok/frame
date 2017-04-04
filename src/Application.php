<?php

namespace John\Frame;
use John\Frame\Exceptions\Route\RouteException;
use John\Frame\Exceptions\Validator\ValidatorException;
use John\Frame\Logger\Logger;
use John\Frame\Request\Request;
use John\Frame\Response\Response;
use John\Frame\Router\Router;
use John\Frame\Service\ServiceContainer;
use Twig_Environment;
use Twig_Loader_Filesystem;


/**
 * Class Application
 * @package John\Frame
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
    private $logger = '';

    /**
     * Application constructor.
     * @param $config
     * @param $log_dir
     */
    public function __construct($config, $log_dir)
    {
        $this->log_dir = $log_dir;
        file_exists($this->log_dir) && is_dir($this->log_dir) ? : mkdir($this->log_dir);
        Logger::setPATH($log_dir);
        $this->logger = Logger::getLogger('root', 'logger.log');
        $this->config = $config;
        if (is_array($this->config) && !is_array($this->config['routes'])) {
            $message = "Routes config not found!";
            $this->logger->info($message);
            die($message);
        }
    }

    /**
     * Application start
     */
    public function start()
    {
        $request = Request::getRequest();
        $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/../src/views');
        $twig = new Twig_Environment($loader, array(
            //'cache' => Constants::RENDER_CACHE_DIR,
        ));
        $service = ServiceContainer::getService();
        $service->setServices('twig', $twig);
        try {
            $router = new Router($this->config['routes']);
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
            $this->logger->debug($e->getMessage());
        } catch (ValidatorException $e) {
            echo $e->getMessage();
            $this->logger->debug($e->getMessage());
        }catch (\Exception $e) {
            echo $e->getMessage();
            $this->logger->debug($e->getMessage());
        }

    }

    /**
     * Application __destruct
     */
    public function __destruct()
    {
        // TODO
    }
}