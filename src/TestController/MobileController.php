<?php

namespace John\Frame\TestController;

use John\Frame\TestModels\MobileModel;

class MobileController extends MainController
{

    public function show(MobileModel $model)
    {
        $filters = $model->getFilters();
        $price = $model->boundaryPrice();
        $title = 'Мобильные телефоны';
        $phones = $model->getLimit(5);
        var_dump($phones);
        $data = compact('title', 'filters', 'price');
        return $this->getRenderer($data);
    }



}