<?php

namespace John\Frame;
use John\Frame\Exceptions\Route\RouteNotFoundException;
use John\Frame\Logger\Logger;
use John\Frame\Renderer\Renderer;
use John\Frame\Request\Request;
use John\Frame\Response\JsonResponse;
use John\Frame\Response\Response;
use John\Frame\Router\Route;
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
    private $response;
    private $renderer;

    /**
     * Application constructor.
     * @param $config
     * @param $log_dir
     */
    public function __construct($config, $log_dir)
    {
//        echo "<pre>";
        $this->log_dir = $log_dir;
        file_exists($this->log_dir) && is_dir($this->log_dir) ? : mkdir($this->log_dir);
        Logger::setPATH($log_dir);
        $this->logger = Logger::getLogger('root', 'logger.log');
        $this->config = $config;
        $this->request = Request::getRequest();
        $loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/Views/');
        if(array_key_exists('views', $this->config)){
            $loader->addPath( $this->config['views'] );
        }
        $twig = new Twig_Environment($loader, array(
            //'cache' => Constants::RENDER_CACHE_DIR,
        ));
        $service = ServiceContainer::getService();
        $service->setServices('twig', $twig);
        $service->setServices('response', $this->response = new Response());
        $service->setServices('renderer', $this->renderer = new Renderer());
        if (is_array($this->config) && !is_array($this->config['routes'])) {
            $message = "Routes config not found!";
            $this->logger->info($message);
//            $response = $this->setError($e->getMessage(), 404);
            die($message);
        }
    }

    /**
     * Application start
     */
    public function start()
    {
        $router = new Router($this->config['routes']);
        try {
            $route = $router->getRoute($this->request);
            if($route){
                $this->response = $this->processRoute($route);
            }
        } catch (RouteNotFoundException $e) {
            $this->response = $this->setError($e->getMessage(), 404);
            $this->logger->debug($e->getMessage());
        } catch (\Exception $e) {
            $this->response = $this->setError($e->getMessage(), 500);
            $this->logger->debug($e->getMessage());
        }
        $this->prepareResponse($this->response)->send();
    }

    /**
     * Process route
     *
     * @param Route $route
     * @return mixed
     * @throws \Exception
     */
    protected function processRoute(Route $route)
    {
        $route_controller = $route->getController();
        if (class_exists($route_controller)) {
            $route_method = $route->getMethod();
            $reflectionClass = new \ReflectionClass($route_controller);
            if ($reflectionClass->hasMethod($route_method)) {
                $controller = $reflectionClass->newInstance();
                $reflectionMethod = $reflectionClass->getMethod($route_method);
                return $reflectionMethod->invokeArgs($controller, $route->getParams());
            } else {
                throw new \Exception(sprintf('Controller method [%s] not found in [%s]', $route_method, $route_controller));
            }
        } else {
            throw new \Exception(sprintf('Controller class [%s] not found', $route_controller));
        }
    }

    /**
     * Prepare content to be processed like response
     *
     * @param   $content
     * @return  Response
     */
    protected function prepareResponse($content):Response
    {
        if($content instanceof Response){
            // Do nothing, just return:
            return $content;
        }

        // Otherwise...
        if($this->request->wantsJson() || is_array($content) || is_object($content)){
            // Deal with Json response:
            $this->response = new JsonResponse($content);
        } else {
            $this->response = new Response($content);
        }

        return $this->response;
    }

    /**
     * Create system error response
     *
     * @param $message
     * @return mixed
     */
    public function setError($message, $code = 500)
    {
        if($this->request->wantsJson()){
            return compact('code', 'message');
        } else {
            //@TODO: Check first if appropriate layout exists...
           $this->renderer->rend('error/'.$code, compact('code', 'message'));
           $this->response->setContent($this->renderer->getRendered());
           return $this->response;
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