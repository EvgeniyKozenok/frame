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
    public function rend(string $view, array $vars = [])
    {
        $service = ServiceContainer::getService();
        $twig = $service->getServices('twig');
        $template = $twig->load($view . ".html.php");
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