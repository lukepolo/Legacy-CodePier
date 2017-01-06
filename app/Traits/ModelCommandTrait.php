<?php

namespace App\Traits;

use App\Models\Command;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

trait ModelCommandTrait
{
    /**
     * @param Site $site
     * @param Model $model
     * @return Command
     */
    private function makeCommand(Site $site, Model $model)
    {
        return Command::create([
            'site_id' => $site->id,
            'commandable_id' => $model->id,
            'commandable_type' => get_class($model),
            'status' => 'Queued',
        ]);
    }
}
