<?php

namespace App\Traits;

use App\Models\Command;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

trait ModelCommandTrait
{
    /**
     * @param Site   $site
     * @param Model  $model
     * @param string $status Determines what command description to use
     *
     * @return Command
     */
    private function makeCommand(Site $site, Model $model, $status)
    {
        return Command::create([
            'site_id' => $site->id,
            'commandable_id' => $model->id,
            'commandable_type' => get_class($model),
            'status' => 'Queued',
            'description' => method_exists($model, 'commandDescription') ? $model->commandDescription($status) : $status,
        ]);
    }
}
