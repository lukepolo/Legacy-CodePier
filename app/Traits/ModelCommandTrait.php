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
            'site_id' => $model->site_id,
            'commandable_id' => $model->id,
            'commandable_type' => get_class($model),
            'status' => 'Queued',
        ]);
    }
}
