<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User\User;

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
        /** @var User $user */
        $user = $request->user();

        if (! empty($user)) {
            if ($user->role === 'admin') {
                return $next($request);
            }

            if (! $user->confirmed) {
                return response()->json('You need to confirm your email address before trying to create a server', 500);
            }

            if (! $user->subscribed()) {
                if ($user->servers->count() > 1) {
                    return $this->toManyServersResponse(1);
                }

                return $next($request);
            }

            $stripePlan = $user->subscription()->active_plan;

            if (str_contains($stripePlan, 'firstmate')) {
                if ($user->servers->count() > 30) {
                    return $this->toManyServersResponse(30);
                }
            } elseif (! str_contains($stripePlan, 'captain')) {
                return response()->json('We don\'t recognize your subscription, please contact support.', 500);
            }
        }

        return $next($request);
    }

    private function toManyServersResponse($maxNumber)
    {
        return response()->json("You have to many servers for your plan (max $maxNumber), please archive some servers.", 401);
    }
}
