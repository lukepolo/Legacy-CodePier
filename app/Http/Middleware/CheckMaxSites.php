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
        if (! $request->user()->subscribed()) {
            if ($request->user()->servers->count() > 1) {
                return $this->toManyServersResponse(1);
            }
        }

        $stripePlan = $request->user()->subscription()->stripe_plan;

        if (str_contains($stripePlan, 'firstmate')) {
            if ($request->user()->servers->count() > 30) {
                return $this->toManyServersResponse(30);
            }
        } elseif (! str_contains($stripePlan, 'captain')) {
            return response()->json('We don\'t recognize your subscription, please contact support.', 500);
        }

        return $next($request);
    }

    private function toManyServersResponse($maxNumber)
    {
        return response()->json("You have to many servers for your plan (max $maxNumber), please archive some servers.", 401);
    }
}
