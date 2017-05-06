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
     * @param array $vars variables in the view
     */
    public function rend(string $view, array $vars = [])
    {
        $injector = Injector::getInjector();
        $twig = $injector->get('twig');
        $template = $twig->load(DIRECTORY_SEPARATOR.$view . ".html.php");
        $this->rendered = $template->render($vars);
    }

    /**
     * @return Renderer|string mixed
     */
    public function getRendered(): string
    {
        return $this->rendered;
    }


}