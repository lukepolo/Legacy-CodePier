<?php

namespace App\Http\Middleware;

use Closure;

class AuthCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('code')) {
            \Session::put('auth_code', $request->get('code'));
        }

        return $next($request);
    }
}
