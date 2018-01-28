<?php

namespace App\Http\Middleware;

use Closure;

class CheckMaxSites
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
        $user = $request->user();

        if (! empty($user)) {
            if ($user->role === 'admin') {
                return $next($request);
            }

            if (! $user->subscribed()) {
                if ($user->sites->count() > 1) {
                    return response()->json('You have to many sites for your plan (max 1), please delete some sites.', 401);
                }
            }
        }

        return $next($request);
    }
}
