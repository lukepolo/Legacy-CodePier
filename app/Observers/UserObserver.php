<?php

namespace App\Observers;

use App\Models\Pile;
use App\Models\User\User;
use App\Models\NotificationSetting;
use App\Models\User\UserNotificationSetting;
use Spatie\Newsletter\NewsletterFacade as NewsLetter;

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
                $user->update([
                    'current_pile_id' => $pile->id,
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

        Newsletter::subscribeOrUpdate($user->email, [
            'FNAME' => $user->name
        ]);
    }

    /**
     * @param User $user
     */
    public function updated(User $user)
    {
        if ($user->isDirty('email') && !empty($user->getOriginal('email'))) {
            Newsletter::updateEmailAddress($user->getOriginal('email'), $user->email);
        }
    }
}
