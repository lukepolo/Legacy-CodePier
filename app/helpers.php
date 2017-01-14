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

if (! function_exists('save_without_events')) {

    /**
     * Gets the version of what is currently installed.
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    function save_without_events(\Illuminate\Database\Eloquent\Model $model)
    {
        $observables = $model->getObservableEvents();

        $model->flushEventListeners();

        $model->save();

        $model->addObservableEvents($observables);

        return $model;
    }
}

if (! function_exists('remove_events')) {

    /**
     * Gets the version of what is currently installed.
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    function remove_events(\Illuminate\Database\Eloquent\Model $model)
    {
        $model->flushEventListeners();

        return $model;
    }
}

if (! function_exists('mix')) {

    function mix($path, $json = false, $shouldHotReload = false)
    {
        if (!$json) {
            static $json;
        }
        if (!$shouldHotReload) {
            static $shouldHotReload;
        }

        if (!$json) {
            $manifestPath = public_path('manifest.json');
            $shouldHotReload = file_exists(public_path('hot'));

            if (!file_exists($manifestPath)) {
                throw new Exception(
                    'The Laravel Mix manifest file does not exist. ' .
                    'Please run "npm run webpack" and try again.'
                );
            }

            $json = json_decode(file_get_contents($manifestPath), true);
        }

        $path = pathinfo($path, PATHINFO_BASENAME);

        if (!array_key_exists($path, $json)) {
            throw new Exception(
                'Unknown file path. Please check your requested ' .
                'webpack.mix.js output path, and try again.'
            );
        }

        return $shouldHotReload
            ? "http://localhost:8080/{$json[$path]}"
            : url($json[$path]);
    }
}