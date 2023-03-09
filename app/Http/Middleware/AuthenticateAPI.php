<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticateAPI
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::guard('api')->check()) {
            return $next($request);
        }

        return response('Unauthorized.', 401);
    }
}
