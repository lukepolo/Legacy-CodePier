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
            $code = $request->get('code');
            if($authCode = \App\Models\AuthCode::where('code', $code)->first()) {
                if(!empty($authCode)) {
                    \Session::put('auth_code', $code);
                }
            }
        }

        return $next($request);
    }
}
