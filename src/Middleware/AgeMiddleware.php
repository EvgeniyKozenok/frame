<?php

namespace John\Frame\Middleware;

use John\Frame\Request\Request;
use John\Frame\Response\RedirectResponse;
use John\Frame\Response\Response;

class AgeMiddleware implements MiddlewareI
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @param \array[] ...$args
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, array ...$args): Response
    {
        $args = array_shift($args);
        $user = 'admin';
        if(!in_array($user, $args))
            return new RedirectResponse('/');
        $response = $next($request);
            return $response;
    }

}