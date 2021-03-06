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
    private $injector;

    /**
     * Application constructor.
     * @param $config
     * @param $log_dir
     */
    public function __construct(array $config, $log_dir)
    {
//        echo "<pre>";
        $this->log_dir = $log_dir;
        file_exists($this->log_dir) && is_dir($this->log_dir) ?: mkdir($this->log_dir);
        Logger::setPATH($log_dir);
        $this->logger = Logger::getLogger('root', 'logger.log');
        $this->config = new Config($config);
        $this->injector = Injector::getInjector($this->config);
        $loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/Views/');
        if ($pathToUserViews = $this->config->__get('views')) {
            $loader->addPath($pathToUserViews);
        }
        $twig = new Twig_Environment($loader, array(
//            'cache' => Constants::RENDER_CACHE_DIR,
        ));
        $this->injector->set('twig', $twig);
    }

    /**
     * Application start
     */
    public function start()
    {
        $router = $this->injector->get('router');
        try {
            $this->request = $this->injector->get('request');
            $route = $router->getRoute($this->request);
            if ($route) {
                $this->response = $this->injector->get('response');
                $this->response = $this->processRoute($route, $route->getCheckMiddlewares());
            }
        } catch (RouteNotFoundException $e) {
            $this->logger->debug($e->getMessage());
            $this->response = $this->setError($e->getMessage(), 404);
        } catch (MiddlewareException $e){
            $this->logger->debug($e->getMessage());
            $this->response = $this->setError($e->getMessage(), 404);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->response = $this->setError($e->getMessage(), 500);
        }
        $this->prepareResponse($this->response)->send();
    }

    /**
     * Process route
     *
     * @param Route $route
     * @param array $middleware
     * @return mixed
     * @throws \Exception
     */
    protected function processRoute(Route $route, array $middleware)
    {
        $route_controller = $route->getController();
        if (class_exists($route_controller)) {
            $route_method = $route->getMethod();
            $reflectionClass = new \ReflectionClass($route_controller);
            if ($reflectionClass->hasMethod($route_method)) {
                $controller = $reflectionClass->newInstance(
                    $this->injector->get('renderer'),
                    $this->injector->get('response'),
                    $this->injector
                );
                $reflectionMethod = $reflectionClass->getMethod($route_method);
                 if ($middleware) {
                     $middle = new Middleware($this->config, $this->injector, $controller, $reflectionMethod, $route->getParams(), $middleware);
                     $response = $middle->getResponse();
                 } else {
                     $response = $reflectionMethod->invokeArgs($controller, $route->getParams());
                 }
                return $response;
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
    public function setError($message = '', $code = 500)
    {
        if ($this->request->wantsJson()) {
            return compact('code', 'message');
        } else {
            $this->renderer = $this->injector->get('renderer');
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