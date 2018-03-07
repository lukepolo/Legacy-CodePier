<?php

namespace App\Http\Controllers\User\Subscription;

use Cache;
use Stripe\Stripe;
use Stripe\Coupon;
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

        if ($request->has('coupon')) {
            if (! $this->validateCoupon($request->get('coupon'))) {
                return $this->invalidCouponResponse();
            }
        }

        $plan = $request->get('plan');

        Cache::forget($user->id.'.card');
        Cache::forget($user->id.'.subscription');

        $subscription = $user->newSubscription('default', $plan);

        $subscription->trialDays(5);

        if ($request->has('coupon')) {
            $subscription->withCoupon($request->get('coupon'));
        }

        $subscription = $subscription->create($request->get('token'));

        $user->update([
            'trial_ends_at' => Carbon::now()->addDays(5),
        ]);

        $subscription->update([
            'active_plan' => $plan,
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

        if ($request->has('coupon')) {
            if (! $this->validateCoupon($request->get('coupon'))) {
                return $this->invalidCouponResponse();
            }
        }

        if (! $user->subscriptions->count()) {
            return response()->json('You do not have a subscription. You need to create one.', 400);
        }

        $plan = $request->get('plan');

        Cache::forget($user->id.'.card');
        Cache::forget($user->id.'.subscription');

        /** @var Subscription $subscription */
        $subscription = $user->subscription('default');

        /** @var \Stripe\Subscription $tempSubscription */
        $tempSubscription = $subscription->asStripeSubscription();

        if ($request->has('coupon')) {
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
            if (
                str_contains($plan, 'yr') && ! str_contains($subscription->stripe_plan, 'yr') ||
                str_contains($plan, 'captain') && str_contains($subscription->stripe_plan, 'firstmate')
            ) {
                $subscription->active_plan = $plan;

                // upgrading
                $subscription
                    ->swap($plan);
            } else {
                // downgrading
                $subscription->trial_ends_at = Carbon::createFromTimestamp(
                    $tempSubscription->current_period_end
                );

                $subscription->active_plan = $subscription->stripe_plan;

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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function invalidCouponResponse()
    {
        return response()->json('Invalid Coupon', 409);
    }

    /**
     * @param $coupon
     * @return bool
     */
    private function validateCoupon($coupon)
    {
        if (! empty($coupon)) {
            Stripe::setApiKey(\Config::get('services.stripe.secret'));
            try {
                $coupon = Coupon::retrieve($coupon);
                return $coupon->valid;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }
}
