<?php

namespace App\Http\Middleware;

use Closure;

class GitCommit
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
        app()->singleton('gitCommit', function() {
            return exec('git rev-parse --short HEAD');
        });

        return $next($request);
    }
}
