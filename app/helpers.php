<?php

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
