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
     * @var array Store all possible Views locations
     */
    public static $views_paths = [];

    /**
     * Add viewsApp lookup path
     *
     * @param string path to folder with viewsApp
     */
    public static function addViewPath($path)
    {
        if (file_exists(realpath($path))) {
            array_unshift(self::$views_paths, $path);
        }
    }

    /**
     * Get all registered view paths
     *
     * @return array
     */
    public static function getViewPaths(): array
    {
        return self::$views_paths;
    }

    /**
     * @param string $view is path to view
     * @param array $vars variables in the view
     */
    public function render(string $view, array $vars = [])
    {
        $service = ServiceContainer::getService();
        $twig = $service->getServices('twig');
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