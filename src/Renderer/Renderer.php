<?php

namespace John\Frame\Renderer;

use John\Frame\Service\ServiceContainer;

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
    public function render(string $view, array $vars = [])
    {
        $twig = ServiceContainer::getServices('twig');
        $template = $twig->load($view . ".html");
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