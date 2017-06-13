<?php

namespace John\Frame\TestController;

use John\Frame\Response\Response;

/**
 * Class IndexController
 * @package John\Frame\TestController
 */
class IndexController extends MainController
{
    /**
     * Index action
     * @return Response
     */
    public function index(): Response
    {
        $title = 'Интернет магазин электроники, бытовой и компьютерной техники';
        $data = compact('title');
        return $this->getRenderer($data);
    }
}