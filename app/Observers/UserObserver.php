<?php

namespace App\Observers;

use App\Mail\ConfirmWelcome;
use App\Mail\Welcome;
use App\Models\NotificationSetting;
use App\Models\Pile;
use App\Models\User\User;
use App\Models\User\UserNotificationSetting;
use Illuminate\Support\Facades\Mail;
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

            if (0 == $index) {
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

        if ('production' === config('app.env')) {
            Newsletter::subscribeOrUpdate($user->email, [
                'FNAME' => $user->name,
            ]);
        }

        if ($user->confirmed) {
//            Mail::to($user)->send(new Welcome($user));
        } else {
            Mail::to($user)->send(new ConfirmWelcome($user));
        }
    }

    /**
     * @param User $user
     */
    public function updated(User $user)
    {
        if ('production' === config('app.env') && $user->isDirty('email') && ! empty($user->getOriginal('email'))) {
            Newsletter::updateEmailAddress($user->getOriginal('email'), $user->email);
        }
    }
}
