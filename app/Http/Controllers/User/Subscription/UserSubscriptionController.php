<?php

namespace App\Http\Controllers\User\Subscription;

use Cache;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserSubscriptionRequest;

class UserSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'card'                 => $user->card(),
            'subscribed'           => $user->subscribed(),
            'subscription'         => $user->subscription(),
            'subscriptionEnds'     => $user->getNextBillingCycle(),
            'subscriptionName'     => $user->getSubscriptionName(),
            'subscriptionPrice'    => $user->getSubscriptionPrice(),
            'subscriptionInterval' => $user->getSubscriptionInterval(),
        ]);
    }

    /**
     * @param UserSubscriptionRequest $request
     *
     * @return mixed
     */
    public function store(UserSubscriptionRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $plan = $request->get('plan');

        Cache::forget($user->id.'.card');
        Cache::forget($user->id.'.subscription');

        if ($user->subscriptions->count()) {
            $user->subscription('default')->swap($plan);
        } else {
            $subscription = $user->newSubscription('default', $plan);

            $subscription->trialDays(5);

            if ($request->has('coupon')) {
                $subscription->withCoupon($request->get('coupon'));
            }

            $subscription->create($request->get('token'));
        }

        return response()->json($user->subscription());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        /** @var User $user */
        $user = $request->user();

        $user->subscription($request->get('subscription', 'default'))->cancel();

        Cache::forget($user->id.'.subscription');

        return response()->json($user->subscription());
    }
}
