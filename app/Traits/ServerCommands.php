<?php

namespace App\Traits;

use App\Models\Command;

trait ServerCommands
{
    public function commands()
    {
        return $this->morphToMany(Command::class, 'commandable');
    }
}
