<?php

namespace App\Http\Middleware;

use Closure;

class VerifySecondAuth
{
    public function handle($request, Closure $next)
    {
        if (! second_authed()) {
            return redirect(action('Auth\SecondAuthController@show'));
        }

        return $next($request);
    }
}
