<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Server\ProvisioningKey;

class VerifyProvisioningKey
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
        if (! ProvisioningKey::findOrFail($request->route('provisioning_key'))) {
            abort(403, 'Invalid provisioning key.');
        }

        return $next($request);
    }
}
