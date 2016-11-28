<?php

namespace App\Traits;

use App\Models\Command;
use Illuminate\Database\Eloquent\Model;

trait ModelCommandTrait
{
    /**
     * @param $model
     * @return Command
     */
    private function makeCommand(Model $model)
    {
        return Command::create([
            'type_id' => $model->id,
            'type' => get_class($model),
            'site_id' => $model->site_id,
        ]);
    }
}
