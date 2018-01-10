<?php

namespace App\Http\Controllers\User\Subscription;

use Cache;
use Carbon\Carbon;
use App\Models\User\User;
use Illuminate\Http\Request;
use Stripe\Error\InvalidRequest;
use Laravel\Cashier\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserSubscriptionRequest;
use App\Http\Requests\User\UserSubscriptionUpdateRequest;

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

        return response()->json(
            $user->subscriptionInfo()
        );
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

        if ($user->subscriptions->count()) {
            return response()->json('You already have a subscription. You need to update it instead of creating a new one.', 400);
        }

        $plan = $request->get('plan');

        Cache::forget($user->id.'.card');
        Cache::forget($user->id.'.subscription');

        $subscription = $user->newSubscription('default', $plan);

        $subscription->trialDays(5);

        if ($request->has('coupon')) {
            $subscription->withCoupon($request->get('coupon'));
        }

        $subscription->create($request->get('token'));

        $user->update([
            'trial_ends_at' => Carbon::now()->addDays(5),
        ]);

        return response()->json(
            $user->subscriptionInfo()
        );
    }

    /**
     * @param UserSubscriptionUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserSubscriptionUpdateRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->subscriptions->count()) {
            return response()->json('You do not have a subscription. You need to create one.', 400);
        }

        $plan = $request->get('plan');

        Cache::forget($user->id.'.card');
        Cache::forget($user->id.'.subscription');

        /** @var Subscription $subscription */
        $subscription = $user->subscription('default');

        if ($request->has('coupon')) {
            /** @var \Stripe\Subscription $tempSubscription */
            $tempSubscription = $subscription->asStripeSubscription();
            $tempSubscription->coupon = $request->get('coupon');
            try {
                $tempSubscription->save();
            } catch (InvalidRequest $e) {
                return response()->json($e->getMessage(), 500);
            }
        }

        if ($subscription->onGracePeriod()) {
            $subscription->resume();
        }

        if ($plan !== $subscription->stripe_plan) {
            if ($subscription->onGracePeriod()) {
                $subscription
                    ->swap($plan);
            } else {
                $subscription
                    ->noProrate()
                    ->swap($plan);
            }
        }

        return response()->json(
            $user->subscriptionInfo()
        );
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

        $user->subscription($request->get('subscription', 'default'))
            ->noProrate()
            ->cancel();

        Cache::forget($user->id.'.subscription');

        return response()->json(
            $user->subscriptionInfo()
        );
    }
}
