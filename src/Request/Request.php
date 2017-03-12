<?php

namespace john\frame\Request;

/**
 * Class Request
 * @package john\frame\Request
 */
class Request
{
   private static $request = null;


   private function __construct()
   {
   }

   private function __clone()
   {
   }

   private function __wakeup()
   {
   }

    /**
     * Returns request
     * @return Request|null
     */
    public static function getRequest(): self
    {
        if(!self::$request)
            self::$request = new self();
        return self::$request;
    }

    /**
     * Get any current server data
     * @param string $dataParam
     * @return string
     */
    public function getData(string $dataParam): string
    {
        return $_SERVER[$dataParam];
    }
}