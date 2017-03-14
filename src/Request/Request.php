<?php

namespace john\frame\Request;

/**
 * Class Request
 * @package john\frame\Request
 */
class Request
{
    /**
     * request
     * @var null
     */
    private static $request = null;

    /**
     * array request headers
     * @var array
     */
    private $headers = [];

    /**
     * array request data
     * @var array
     */
    private $requestData = [];

    /**
     * array request query string params
     * @var array
     */
    private $queryParams = [];

    /**
     * Request constructor.
     */
    private function __construct()
    {
        $this->queryParams = $_REQUEST;

        foreach ($_SERVER as $param => $value) {
            if (substr($param, 0, 5) === "HTTP_") {
                $param = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($param, 5)))));
                $this->headers[$param] = $value;
            } else
                $this->requestData[$param] = $value;
        }
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
        if (!self::$request)
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
        $data = explode('?', $_SERVER[$dataParam]);
        return $data[0];
    }

    /**
     * Get request header
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get request params
     *
     * @return array|string
     */
    public function getQueryParams(): string
    {
        $queryData = [];
        foreach (func_get_args() as $param) {
            !array_key_exists($param, $this->queryParams) ? : $queryData[$param] = $this->queryParams[$param];
        }
        return func_num_args() > 0 && count($queryData) > 0 ? $this->toString($queryData) : $this->toString($this->queryParams);
    }

    /**
     * Converts associative array to string
     * @param $array
     * @return string
     */
    private function toString($array): string
    {
        $str = '';
        foreach ($array as $item => $value)
            $str .= "$item=$value<br>";
        return $str;
    }
}