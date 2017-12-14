<?php

namespace App\Http\Middleware;

use Closure;

class CheckSiteCreationLimit
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

        if (! $user->subscribed()) {
            if ($user->sites->count() >= 1) {
                return response()->json('You cannot create another site, as free plans only allow 1 site.', 401);
            }
        }

        return $next($request);
    }
}
