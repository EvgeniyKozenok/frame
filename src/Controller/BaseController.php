<?php

namespace John\Frame\Controller;

use John\Frame\Renderer\Renderer;
use John\Frame\Response\Response;
use John\Frame\Service\ServiceContainer;

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
        $services = ServiceContainer::getService();
        $this->response = $services->getServices('response');
        $this->renderer = $services->getServices('renderer');
    }


}