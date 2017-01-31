<?php

namespace App\Http\Controllers\User\Subscription;

use Stripe\Token;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserSubscriptionRequest;

class UserSubscriptionController extends Controller
{
    /**
     * UserSubscriptionController constructor.
     */
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\Auth::user()->subscription());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserSubscriptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserSubscriptionRequest $request)
    {
        $user = \Auth::user();

        if ($request->has('number')) {
            $cardToken = Token::create([
                'card' => [
                    'number'    => $request->get('number'),
                    'exp_month' => $request->get('exp_month'),
                    'exp_year'  => $request->get('exp_year'),
                    'cvc'       => $request->get('cvc'),
                ],
            ]);

            if ($user->hasStripeId()) {
                $user->updateCard($cardToken->id);
            }
        }

        if ($user->subscribed()) {
            $user->subscription('default')->swap($request->get('plan'));
        } else {
            $user->newSubscription('default', $request->get('plan'))->create(isset($cardToken) ? $cardToken->id : null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        \Auth::user()->subscription('default')->cancel();
    }
}
