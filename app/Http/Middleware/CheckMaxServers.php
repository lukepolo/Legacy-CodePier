<?php

namespace App\Http\Middleware;

use Closure;

class CheckMaxServers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user()->subscribed()) {
            if ($request->user()->servers->count() > 1) {
                return response()->json('You have to many sites for your plan, please delete some sites.', 401);
            }
        }

        return $next($request);
    }
}
