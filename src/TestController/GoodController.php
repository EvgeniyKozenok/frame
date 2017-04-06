<?php

namespace John\Frame\TestController;

use John\Frame\Controller\BaseController;
use John\Frame\Response\Response;

/**
 * Class GoodController
 * @package John\Frame\TestController
 */
class GoodController extends BaseController
{

    /**
     * Return response by some good
     *
     * @param integer $id some good
     * @return Response
     */
    public function getOneGood($id): Response
    {
        $this->render->render('index', [
            'title' => "Good: $id",
        ]);
        $this->response->setContent($this->render->getRendered());
        return $this->response;
    }

    /**
     * Return response by some good with params
     *
     * @param integer $id some good
     * @param string $name param of some good
     * @return Response
     */
    public function getOneGoodWithParam($id, $name): Response
    {
        $this->render->render('index', [
            'title' => "Good: $id",
            'param' => $name
        ]);
        $this->response->setContent($this->render->getRendered());
        return $this->response;
    }

    /**
     * Return response by all goods
     *
     * @return Response
     */
    public function getAllGoods(): Response
    {
        $this->render->render('index', [
            'title' => 'All Goods',
            'currentDate' => date('d:m:Y H:i')
        ]);
        $this->response->addHeader('Cache-Control', ' no-cache, must-revalidate');
        return $this->response;
    }

}