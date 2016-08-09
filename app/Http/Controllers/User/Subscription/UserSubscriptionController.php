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
    public $user;

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
        if ($request->has('number') && $request->has('exp_month') && $request->has('exp_year') && $request->has('cvc')) {

            $cardToken = Token::create([
                "card" => [
                    "number" => $request->get('number'),
                    "exp_month" => $request->get('exp_month'),
                    "exp_year" => $request->get('exp_year'),
                    "cvc" => $request->get('cvc')
                ]
            ]);

            $this->user->updateCard($cardToken->id);
        }

        if ($this->user->subscribed()) {
            $this->user->subscription('default')->swap($request->get('plan'));
        } else {
            $this->user->newSubscription('default', $request->get('plan'))->create();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this->user->subscription('default')->cancel();
    }

    /**
     * Downloads an invoice from stripe
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadInvoice($id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        return \Auth::user()->downloadInvoice($id, [
            'vendor' => 'CodePier',
            'product' => 'Server Management',
        ]);
    }
}
