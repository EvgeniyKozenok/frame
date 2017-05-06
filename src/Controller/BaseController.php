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
class BaseController
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
     * Created render and response object
     *
     * BaseController constructor.
     * @internal param $response
     */
    public function __construct()
    {
        $injector = Injector::getInjector();
        $this->response = $injector->get('Response');
        $this->renderer = $injector->get('Renderer');
    }


}