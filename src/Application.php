<?php

namespace John\Frame;

use John\Frame\Config\Config;
use John\Frame\DI\Injector;
use John\Frame\Exceptions\Middleware\MiddlewareException;
use John\Frame\Exceptions\Route\RouteNotFoundException;
use John\Frame\Logger\Logger;
use John\Frame\Middleware\Middleware;
use John\Frame\Response\JsonResponse;
use John\Frame\Response\Response;
use John\Frame\Router\Route;
use John\Frame\Router\Router;
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
    private $config;
    private $log_dir = null;
    private $logger = '';
    private $response;
    private $renderer;
    private $request;

    /**
     * Application constructor.
     * @param $config
     * @param $log_dir
     */
    public function __construct($config = [], $log_dir)
    {
        $this->log_dir = $log_dir;
        file_exists($this->log_dir) && is_dir($this->log_dir) ?: mkdir($this->log_dir);
        Logger::setPATH($log_dir);
        $this->logger = Logger::getLogger('root', 'logger.log');
        $this->config = new Config($config);
        $injector = Injector::getInjector($this->config);
        $this->request = $injector->get('Request');
        $this->response = $injector->get('Response');
        $this->renderer = $injector->get('renderer');
        $loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/Views/');
        if ($pathToUserViews = $this->config->__get('views')) {
            $loader->addPath($pathToUserViews);
        }
        $twig = new Twig_Environment($loader, array(//'cache' => Constants::RENDER_CACHE_DIR,
        ));
        $injector->set('twig', $twig);
    }

    /**
     * Application start
     */
    public function start()
    {
        $router = new Router($this->config->get('routes'));
        try {
            $route = $router->getRoute($this->request);
            if ($route) {
                $this->response = $this->processRoute($route);
                new Middleware($this->config, $route->getCheckMiddlewares());
                if ($this->response->code !== 200)
                    $this->response = $this->setError($this->response->message, $this->response->code);
            }
        } catch (RouteNotFoundException $e) {
            $this->response = $this->setError($e->getMessage(), 404);
            $this->logger->debug($e->getMessage());
        } catch (MiddlewareException $e){
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
    protected function prepareResponse($content): Response
    {
        if ($content instanceof Response) {
            return $content;
        }

        if ($this->request->wantsJson() || is_array($content) || is_object($content)) {
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
     * @param int $code
     * @return mixed
     */
    public function setError($message, $code = 500)
    {
        if ($this->request->wantsJson()) {
            return compact('code', 'message');
        } else {
            $this->renderer->rend('error/' . $code, compact('code', 'message'));
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