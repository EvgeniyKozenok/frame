<?php

namespace john\frame\Response;

/**
 * Class Response
 * @package john\frame\Response
 */
class Response
{

    /**
     * HTTP Status messages
     */
    const STATUS_MSGS = [
        '200' => 'Ok',
        '301' => 'Moved permanently',
        '302' => 'Moved temporary',
        '401' => 'Auth required',
        '403' => 'Access denied',
        '404' => 'Not found',
        '500' => 'Server error'
    ];

    /**
     * @var int Response code
     */
    public $code = 200;
    /**
     * @var string response content
     */
    protected $content = '';

    /**
     * @var array response headers
     */
    protected $headers = [];


    /**
     * Response constructor.
     * @param $content
     * @param int $code
     */
    public function __construct($content = '', $code = 200)
    {
        $this->content = $content;
        $this->code = $code;
        $this->addHeader('Content-Type','text/html');
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Add header
     *
     * @param $key
     * @param $value
     */
    public function addHeader($key, $value){
        $this->headers[$key] = $value;
    }

    /**
     * Send response
     */
    public function send(){
        $this->sendHeaders();
        $this->sendBody();
        exit();
    }

    /**
     * Send headers
     */
    public function sendHeaders(){
        header($_SERVER['SERVER_PROTOCOL'] . " " . $this->code . " " . self::STATUS_MSGS[$this->code]);
        if(!empty($this->headers)){
            foreach($this->headers as $key => $value){
                header($key.": ". $value);
            }
        }
    }

    /**
     * Send response playLoad
     */
    public function sendBody(){
        echo $this->content;
    }
}