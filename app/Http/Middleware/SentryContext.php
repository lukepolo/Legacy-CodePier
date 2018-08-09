<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SentryContext
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
        // TODO - move this
        if (auth()->check() && app()->bound('sentry')) {
            $sentry = app('sentry');
            $sentry->user_context(['id' => Auth::user()->id, 'name' => Auth::user()->name]);
        }

        return $next($request);
    }
}
