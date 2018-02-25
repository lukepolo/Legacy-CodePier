<?php

namespace App\Listeners;

use Carbon\Carbon;

class LoginSuccessful
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $event->user->last_login_at = Carbon::now();
        $event->user->save();
    }
}
