<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Token;

/**
 * Class UserSubscriptionController
 * @package App\Http\Controllers
 */
class UserSubscriptionController extends Controller
{
    protected $user;

    /**
     * UserSubscriptionController constructor.
     */
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->user = \Auth::user();
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
        if (\Request::has('number') && \Request::has('exp_month') && \Request::has('exp_year') && \Request::has('cvc')) {

            $cardToken = Token::create([
                "card" => [
                    "number" => \Request::get('number'),
                    "exp_month" => \Request::get('exp_month'),
                    "exp_year" => \Request::get('exp_year'),
                    "cvc" => \Request::get('cvc')
                ]
            ]);

            $this->user->updateCard($cardToken->id);
        }

        if ($this->user->subscribed()) {
            $this->user->subscription('default')->swap(\Request::get('plan'));
        } else {
            $this->user->newSubscription('default', \Request::get('plan'))->create();
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this->user->subscription('default')->cancel();

        return back();
    }
}
