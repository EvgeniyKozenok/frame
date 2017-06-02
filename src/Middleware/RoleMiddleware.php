<?php

namespace John\Frame\Middleware;

use John\Frame\Request\Request;
use John\Frame\Response\Response;

class RoleMiddleware implements MiddlewareI
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @param \array[] ...$args
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, array ...$args): Response
    {
        $response = $next($request);
        return $response;
    }

}