<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 15.05.17
 * Time: 22:51
 */

namespace John\Frame\Middleware;

use John\Frame\Request\Request;
use John\Frame\Response\Response;

class TestMiddleware
{
    public function handle(Request $request, \Closure $next, array ...$args): Response
    {
        return $next($request);
    }
}