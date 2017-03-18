<?php

namespace john\frame\Render;

use Twig_Environment;
use Twig_Loader_Filesystem;
use john\frame\Constants\Constants;

/**
 * Rendering
 *
 * Class Render
 * @package john\frame\Render
 */
class Render extends Constants
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
        $loader = new Twig_Loader_Filesystem(Constants::TEMPLATES_DIR);
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