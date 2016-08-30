<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Token;

/**
 * Class UserSubscriptionController
 * @package App\Http\Controllers
 */
class UserSubscriptionController extends Controller
{
    /**
     * UserSubscriptionController constructor.
     */
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();

        if ($request->has('number') && $request->has('exp_month') && $request->has('exp_year') && $request->has('cvc')) {

            $cardToken = Token::create([
                "card" => [
                    "number" => $request->get('number'),
                    "exp_month" => $request->get('exp_month'),
                    "exp_year" => $request->get('exp_year'),
                    "cvc" => $request->get('cvc')
                ]
            ]);

            if($user->hasStripeId()) {
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        \Auth::user()->subscription('default')->cancel();
    }
}
