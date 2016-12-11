<?php

namespace App\Observers;

use App\Models\Pile;
use App\Models\User\User;
use App\Models\NotificationSetting;
use App\Models\User\UserNotificationSetting;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        // lets create some piles for them
        $defaultPiles = [
            'dev',
            'qa',
            'production',
        ];
        foreach ($defaultPiles as $index => $defaultPile) {
            $pile = Pile::create([
                'name'    => $defaultPile,
                'user_id' => $user->id,
            ]);

            if ($index == 0) {
                \Auth::user()->update([
                    'current_pile' => $pile->id,
                ]);
            }
        }

        foreach (NotificationSetting::all() as $notificationSetting) {
            UserNotificationSetting::create([
                'user_id' => $user->id,
                'services' => $notificationSetting->services,
                'notification_setting_id' => $notificationSetting->id,
            ]);
        }
    }
}
