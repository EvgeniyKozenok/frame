<?php

namespace John\Frame\Response;

/**
 * Class RedirectResponse
 * @package John\Frame\Response
 */
class RedirectResponse extends Response
{
    /**
     * RedirectResponse constructor.
     *
     * @param $redirect_uri
     * @param int $code
     */
    public function __construct($redirect_uri, $code = 301)
    {
        $this->code = $code;
        $this->addHeader('Location', $redirect_uri);
    }
}