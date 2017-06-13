<?php

namespace John\Frame\TestController;

use John\Frame\Controller\BaseController;
use John\Frame\Response\Response;
use John\Frame\TestModels\GoodModel;

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
     * @param GoodModel $model
     * @return Response
     */
    public function getOneGood($id, GoodModel $model): Response
    {
        $id = 3;
        $title = "Good: $id";
        $this->data = compact('title', 'data');
        return $this->getRenderer();
    }

    /**
     * Return response by some good with params
     *
     * @param integer $id some good
     * @param GoodModel $mod
     * @return Response
     * @internal param GoodModel $model
     */
    public function getOneGoodWithParam($name, $id, GoodModel $mod): Response
    {

//        $data = $mod->findOne(8);
        $title = "Good: $id";
        $this->data = compact('title', 'data');
        return $this->getRenderer();
    }

    /**
     * Return response by all goods
     *
     * @return Response
     */
    public function getAllGoods(): Response
    {
        $this->data = [
            'title' => 'All Goods',
            'currentDate' => date('d:m:Y H:i:s')
        ];
        return $this->getRenderer();
    }

}