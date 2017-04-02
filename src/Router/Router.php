<?php

namespace John\Frame\Router;

use John\Frame\Exceptions\Config\UndefDataException;
use John\Frame\Exceptions\Route\InvalidRouteNameException;
use John\Frame\Exceptions\Route\RouteNotFoundException;
use John\Frame\Exceptions\Route\RouteNotKeyException;
use John\Frame\Request\Request;
use John\Frame\Validator\Validator;

/**
 * Class Router
 * @package John\Frame\Router
 */
class Router
{
    const PATTERN = "pattern";
    const VARIABLES = "variables";
    const METHOD = "method";
    const ACTION = "action";
    const REGEXP = "regexp";
    const CONTROLLER_NAME = "controller_name";
    const CONTROLLER_METHOD = "controller_method";
    const DEFAULT_VAR_REGEXP = "[^\/]+";

    /**
     * @var array
     */
    private $routes = [];

    /**
     * Router constructor.
     * @param array $config
     * @throws UndefDataException
     */
    public function __construct(array $config)
    {
         $validator = new Validator($config, [
            'config_key' => ['key_verification_rule' => [self::ACTION, self::PATTERN], 'not_start_from' => ['t', 'p']]
        ]);
        if($validator->validate()) {
            foreach ($config as $item => $value) {
                if ($this->getController($value[self::ACTION], 1) == '') {
                    throw new UndefDataException("Value of the field '" . self::ACTION . "' in config should contain '@' delimiter");
                }
                $existed_variables = $this->getExistedVariables($value[self::PATTERN]);
                $variables = isset($value[self::VARIABLES]) ? $value[self::VARIABLES] : null;
                $this->routes[$item] = [
                    self::REGEXP => "/" . $this->getRegexpFromRoute($value[self::PATTERN], $variables, $existed_variables) . "/",
                    self::METHOD => isset($value[self::METHOD]) && ($value[self::METHOD] != '') ? $value[self::METHOD] : "GET",
                    self::CONTROLLER_NAME => $this->getController($value[self::ACTION]),
                    self::CONTROLLER_METHOD => $this->getController($value[self::ACTION], 1),
                    self::VARIABLES => $existed_variables
                ];
            }
        } else {
            $array = $validator->getErrors();
            $message = '';
            foreach ($array as $item => $value) {
                foreach ($value['config_key'] as $field => $string) {
                    $message .= "$string <br />";
                }
            }
            throw new UndefDataException($message);
        }
    }

    /**
     * @param Request $request
     * @return Route
     * @throws \Exception
     */
    public function getRoute(Request $request): Route
    {
        $uri = $request->getData("REQUEST_URI");
        foreach ($this->routes as $route => $param) {
            if (preg_match($param[self::REGEXP], $uri, $preg_match_array) &&
                $request->getData("REQUEST_METHOD") == $param[self::METHOD]
            ) {
                $preg_match_array = $this->getParams($preg_match_array, $param[self::VARIABLES]);
                return new Route($route, $param[self::CONTROLLER_NAME], $param[self::CONTROLLER_METHOD], $preg_match_array);
            }
        }
        throw new RouteNotFoundException("Not found route in config for uri: $uri");
    }

    /**
     * @param string $action
     * @param int $position
     * @return string
     */
    private function getController(string $action, int $position = 0): string
    {
        $string = count(explode("@", $action)) == 1 ? '' : explode("@", $action)[$position];
        return $string;
    }

    /**
     * @param string $pattern
     * @return array
     */
    private function getExistedVariables(string $pattern): array
    {
        preg_match_all("/{.+}/U", $pattern, $variables);
        return array_map(function ($value) {
            return substr($value, 1, strlen($value) - 2);
        }, $variables[0]);
    }

    /**
     * @param $pattern
     * @param $variables
     * @param $existed_variables
     * @return string
     */
    private function getRegexpFromRoute($pattern, $variables, $existed_variables): string
    {
        $pattern = "^" . str_replace("/", "\/", $pattern) . "$";
        if ($variables) {
            for ($i = 0; $i < count($existed_variables); $i++) {
                $temp = (array_key_exists($existed_variables[$i], $variables)) ?
                    $variables[$existed_variables[$i]] :
                    self::DEFAULT_VAR_REGEXP;
                $pattern = str_replace("{" . $existed_variables[$i] . "}", "(" . $temp . ")", $pattern);
            }
        }
        return $pattern;
    }

    /**
     * @param array $preg_match_array
     * @param array $existed_variables
     * @return array
     */
    private function getParams(array $preg_match_array, array $existed_variables)
    {
        $preg_match_array = array_slice($preg_match_array, 1);
        for ($i = 0; $i < count($existed_variables); $i++) {
            $preg_match_array[$existed_variables[$i]] = $preg_match_array[$i];
            unset($preg_match_array[$i]);
        }
        return $preg_match_array;
    }

    /**
     * Construct link from inner data
     *
     * @param string $route_name
     * @param array $params
     * @return string
     * @throws InvalidRouteNameException
     * @throws RouteNotKeyException
     */
    public function getLink(string $route_name, array $params = []): string
    {
        if (array_key_exists($route_name, $this->routes)) {
            $link = $this->routes[$route_name][self::REGEXP];
            $link = str_replace("\\", "", substr_replace(substr_replace($link, '', strlen($link) - 2, 2), '', 0, 2));
            preg_match_all("/\(.*\)/U", $link, $keys);
            $existing_parameters = $this->routes[$route_name][self::VARIABLES];
            for ($i = 0; $i < count($existing_parameters); $i++) {
                if (!array_key_exists($existing_parameters[$i], $params)) {
                    throw new RouteNotKeyException("Key \"$existing_parameters[$i]\" is required for route \"$route_name\"");
                } else {
                    $link = str_replace($keys[0][$i], $params[$existing_parameters[$i]], $link);
                }
            }
        } else {
            throw new InvalidRouteNameException("Route with name \"$route_name\" was not found in config");
        }
        return $link;
    }
}
