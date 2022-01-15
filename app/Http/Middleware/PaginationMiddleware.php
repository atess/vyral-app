<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaginationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $data = $response->getData(true);

        if (isset($data['links'])) {
            unset($data['links']);
        }
        if (isset($data['meta'], $data['meta']['links'])) {
            unset($data['meta']['links']);

            if (isset($data['meta']['path']))
                unset($data['meta']['path']);
        }

        $response->setData($data);

        return $response;
    }
}
