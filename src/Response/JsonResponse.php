<?php

namespace John\Frame\Response;

/**
 * Class JsonResponse
 * @package John\Frame\Response
 */
class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     * @param $content
     * @param int $code
     */
    public function __construct($content, $code = 200)
    {
        parent::__construct($content, $code);
        $this->addHeader('Content-Type','application/json');
    }
    /**
     * Send content to the client
     */
    public function sendBody(){
        echo json_encode($this->content);
    }
}