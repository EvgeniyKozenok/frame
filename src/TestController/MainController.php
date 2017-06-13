<?php

namespace John\Frame\TestController;


use John\Frame\Controller\BaseController;
use John\Frame\Router\Route;

class MainController extends BaseController
{
    /**
     * @var this route
     */
    protected $route;

    /**
     * @var array data fo this view
     */
    protected $data = [];

    /**
     * set  this route for controller
     * @param Route $route
     */
    public function setRoute(Route $route){
        $this->route = $route;
    }

    /**
     * Function for render data
     *
     * @param array $data
     * @param string $view
     * @param string $controller
     * @return \John\Frame\Response\Response
     */
    protected function getRenderer(array $data, string $view = '', string $controller = '')
    {
        $view = $view ? : $this->route->getMethod();
        $controller = $controller ? : $this->route->getController();
        $this->data = array_merge($this->getSidebarData(), $data);
        $this->renderer->rend($this->injector, $view, $controller, $this->data);
        $this->response->setContent($this->renderer->getRendered());
        return $this->response;
    }

    /**
     * Function that get sidebar data for site
     * @return array
     */
    private function getSidebarData(): array {
        $model = $this->injector->get('John\\Frame\\TestModels\\CategoryModel');
        $goods = $model->getGoods();
        $categories = $model->getCategories();
        return compact('goods', 'categories');
    }

}