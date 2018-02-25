<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserSubscriptionCardUpdateRequest;
use App\Models\User\User;
use Cache;

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
