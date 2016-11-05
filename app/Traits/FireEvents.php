<?php

namespace App\Traits;

trait FireEvents
{
    /**
     * @param $event
     * @param bool $halt
     */
    public function fire($event, $halt = false)
    {
        $this->fireModelEvent($event, $halt);
    }
}
