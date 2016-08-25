<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     * @param $userId
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        return response()->json(User::findOrFail($userId)->subscription());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $userId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($request->has('number') && $request->has('exp_month') && $request->has('exp_year') && $request->has('cvc')) {

            $cardToken = Token::create([
                "card" => [
                    "number" => $request->get('number'),
                    "exp_month" => $request->get('exp_month'),
                    "exp_year" => $request->get('exp_year'),
                    "cvc" => $request->get('cvc')
                ]
            ]);

            $user->updateCard($cardToken->id);
        }

        if ($user->subscribed()) {
            $user->subscription('default')->swap($request->get('plan'));
        } else {
            $user->newSubscription('default', $request->get('plan'))->create();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $userId
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        User::findOrFail($userId)->subscription('default')->cancel();
    }
}
