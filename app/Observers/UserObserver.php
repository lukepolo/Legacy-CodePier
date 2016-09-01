<?php

namespace App\Observers;

use App\Models\Pile;
use App\Models\User;

/**
 * Class UserObserver.
 */
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
        foreach ($defaultPiles as $defaultPile) {
            Pile::create([
                'name'    => $defaultPile,
                'user_id' => $user->id,
            ]);
        }
    }
}
