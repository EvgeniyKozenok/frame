<?php

namespace john\frame\Controller;

use john\frame\Render\Render;
use john\frame\Response\Response;

/**
 * Base Controller fo general properties
 *
 * Class BaseController
 * @package john\frame\Controller
 */
class BaseController
{

    /**
     * @var Response
     */
    protected $response;
    /**
     * @var Render
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
        $this->render = new Render();
    }


}