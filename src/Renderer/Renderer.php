<?php

namespace John\Frame\Renderer;

use John\Frame\DI\Injector;

/**
 * Rendering
 *
 * Class Renderer
 * @package John\Frame\Renderer
 */
class Renderer
{

    /**
     * @var string
     */
    private $rendered;

    /**
     * @param string $view is path to view
     * @param string $controller
     * @param Injector $injector
     * @param array $vars variables in the view
     */
    public function rend(Injector $injector, string $view, string $controller,array $vars = [])
    {
        $twig = $injector->get('twig');
        $controller = $this->getControllerName($controller);
        $view = $this->getView($controller, lcfirst($view), $twig);
        $template = $twig->load($view);
        $this->rendered = $template->render($vars);
    }

    /**
     * @return Renderer|string mixed
     */
    public function getRendered(): string
    {
        return $this->rendered;
    }

    /**
     * Get view for this route
     *
     * @param string $controller
     * @param string $view
     * @param $twig
     * @return string
     * @throws \Exception
     */
    private function getView(string $controller, string $view, $twig)
    {
        foreach ($twig->getLoader()->getPaths() as $path){
            if (file_exists($path.DIRECTORY_SEPARATOR.$controller.DIRECTORY_SEPARATOR.$view . ".html.twig")) {
                return DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $view . ".html.twig";
            }
            if (file_exists($path.DIRECTORY_SEPARATOR.$view . ".html.twig")) {
                return DIRECTORY_SEPARATOR . $view . ".html.twig";
            }
        }
        throw new \Exception("Template $controller/$view.html.twig cannot find in your filesystem");
    }

    private function getControllerName(string $controller): string
    {
        $controller = explode("\\", $controller);
        return lcfirst(array_pop($controller));
    }


}