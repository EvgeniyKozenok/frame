<?php

namespace John\Frame\Controller;

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
    protected $render;

    /**
     * Created render and response object
     *
     * BaseController constructor.
     * @internal param $response
     */
    public function __construct()
    {
        $this->response = new Response();
        $this->render = new Renderer();
    }


}