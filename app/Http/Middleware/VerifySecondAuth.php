<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\SecondAuthController;
use Closure;
use Illuminate\Support\Facades\Session;

class VerifySecondAuth
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if(
            $user &&
            $user->second_auth_active &&
            (
                empty(Session::get(SecondAuthController::SECOND_AUTH_SESSION)) ||
                Session::get(SecondAuthController::SECOND_AUTH_SESSION)->timestamp !== $user->second_auth_updated_at->timestamp
            )
        ) {
            return redirect(action('Auth\SecondAuthController@show'));
        }

        return $next($request);
    }
}