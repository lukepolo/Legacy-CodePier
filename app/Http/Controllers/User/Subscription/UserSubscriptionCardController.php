<?php

namespace App\Http\Controllers\User\Subscription;

use Cache;
use App\Models\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserSubscriptionCardUpdateRequest;

class UserSubscriptionCardController extends Controller
{
    /**
     * @param UserSubscriptionCardUpdateRequest $request
     *
     * @return mixed
     */
    public function store(UserSubscriptionCardUpdateRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->updateCard($request->get('token'));

        Cache::forget($user->id.'.card');

        return response()->json(
            $user->subscriptionInfo()
        );
    }
}
