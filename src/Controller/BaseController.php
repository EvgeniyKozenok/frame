<?php

namespace John\Frame\Controller;

use John\Frame\DI\Injector;
use John\Frame\Renderer\Renderer;
use John\Frame\Response\Response;

/**
 * Base Controller fo general properties
 *
 * Class BaseController
 * @package John\Frame\Controller
 */
abstract class BaseController
{

    /**
     * @var Response
     */
    protected $response;
    /**
     * @var Renderer
     */
    protected $renderer;
    /**
     * @var Injector
     */
    protected $injector;

    /**
     * Created render and response object
     *
     * BaseController constructor.
     * @param Renderer $renderer
     * @param Response $response
     * @param Injector $injector
     */
    public function __construct(Renderer $renderer, Response $response, Injector $injector)
    {
        $this->response = $response;
        $this->renderer = $renderer;
        $this->injector = $injector;
    }

}