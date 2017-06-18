<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Auth\SecondAuthController;

class VerifySecondAuth
{
    public function handle($request, Closure $next)
    {
        if (!second_authed()) {
            return redirect(action('Auth\SecondAuthController@show'));
        }

        return $next($request);
    }
}
