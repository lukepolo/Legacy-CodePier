<?php

namespace App\Observers\Site;

use App\Models\Site\Lifeline;
use App\Events\Site\LifeLineUpdated;
use App\Notifications\LifeLineCheckedIn;

class LifelineObserver
{
    public function updating(Lifeline $lifeline)
    {
        if ($lifeline->isDirty('sent_notifications')) {
        $lifeline->notify(new LifeLineCheckedIn);
        }
    }

    public function updated(Lifeline $lifeline)
    {
        broadcast(new LifeLineUpdated($lifeline));
    }
}
