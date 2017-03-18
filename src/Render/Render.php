<?php

namespace john\frame\Render;

use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Rendering
 *
 * Class Render
 * @package john\frame\Render
 */
class Render
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
        $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/../src/views');
        $twig = new Twig_Environment($loader, array(
            //'cache' => Constants::RENDER_CACHE_DIR,
        ));
        $template = $twig->load($view . ".html");
        $this->rendered = $template->render($vars);
    }

    /**
     * @return Render|string mixed
     */
    public function getRendered(): string
    {
        return $this->rendered;
    }


}