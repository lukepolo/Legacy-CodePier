<?php

namespace App\Traits;

use App\Models\Command;

trait ServerCommands
{
    public function commands()
    {
        return $this->morphMany(Command::class, 'commandable');
    }
}
