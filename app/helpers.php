<?php

if (! function_exists('current_version')) {

    /**
     * Gets the version of what is currently installed.
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
     * @param \Illuminate\Database\Eloquent\Model $model
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
