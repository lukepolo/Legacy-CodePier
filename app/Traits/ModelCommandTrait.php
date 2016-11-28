<?php

namespace App\Traits;

use App\Models\Command;

trait ModelCommandTrait
{
    /**
     * @param $model
     * @param $siteId
     * @return Command
     */
    private function makeCommand($model, $siteId)
    {
        return Command::create([
            'type' => get_class($model),
            'site_id' => $siteId,
        ]);
    }
}
