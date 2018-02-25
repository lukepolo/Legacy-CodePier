<?php

use App\Http\Controllers\Auth\SecondAuthController;

if (! function_exists('current_version')) {
    /**
     * Gets the version of what is currently installed.
     *
     * @return mixed
     */
    function current_version()
    {
        return exec('git --git-dir '.base_path().'/.git rev-parse --short HEAD');
    }
}

if (! function_exists('strip_relations')) {
    /**
     * Strips the relations from a model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    function strip_relations(\Illuminate\Database\Eloquent\Model $model)
    {
        foreach ($model->getRelations() as $relation => $data) {
            unset($model->$relation);
        }

        return $model;
    }
}

if (! function_exists('create_system_service')) {
    function create_system_service($service, \App\Models\Server\Server $server)
    {
        /** @var \App\Services\Systems\SystemService $systemService */
        $systemService = app(\App\Contracts\Systems\SystemServiceContract::class);

        return $systemService->createSystemService($service, $server);
    }
}

if (! function_exists('unique_hash')) {
    /**
     * Gets Makes a new hash based on redis.
     */
    function unique_hash()
    {
        return Ramsey\Uuid\Uuid::uuid1();
    }
}

if (! function_exists('is_domain')) {
    /**
     * Gets Makes a new hash based on redis.
     *
     * @param string $domain
     *
     * @return bool
     */
    function is_domain($domain)
    {
        return preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $domain) > 0;
    }
}

if (! function_exists('second_authed')) {
    /**
     * Checks to see if the user has been second authed.
     *
     * @return bool
     */
    function second_authed()
    {
        $user = \Auth::user();

        if ($user && $user->second_auth_active) {
            return
                ! empty(Session::get(SecondAuthController::SECOND_AUTH_SESSION)) &&
                Session::get(SecondAuthController::SECOND_AUTH_SESSION) == $user->second_auth_updated_at;
        }

        return true;
    }
}

if (! function_exists('cents_to_dollars')) {
    /**
     * Converts cents to dollars.
     *
     * @return mixed
     */
    function cents_to_dollars($cents)
    {
        return '$'.number_format(($cents / 100), 2, '.', ' ');
    }
}

if (! function_exists('ddd')) {
    /**
     * Developer conveinence.
     *
     * @return mixed
     */
    function ddd(...$args)
    {
        http_response_code(500);
        call_user_func_array('dd', $args);
    }
}
