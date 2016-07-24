<?php

namespace App\Http\Controllers;

use Stripe\Token;

/**
 * Class PaymentController
 * @package App\Http\Controllers
 */
class PaymentController extends Controller
{
    public $user;

    /**
     * PaymentController constructor.
     */
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->user = \Auth::user();
    }

    /**
     * Handles the subscriptions for the user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSubscription()
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

        if ($this->user->subscriptions()->count()) {
            $this->user->subscription('default')->swap(\Request::get('plan'));
        } else {
            $this->user->newSubscription('default', \Request::get('plan'))->create();
        }

        return back();
    }

    public function getCancelSubscription()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this->user->subscription('default')->cancel();

        return back();
    }

    public function getUserInvoice($invoiceId)
    {
        return \Auth::user()->downloadInvoice($invoiceId, [
            'vendor'  => 'CodePier',
            'product' => 'Server Management',
        ]);
    }
}
