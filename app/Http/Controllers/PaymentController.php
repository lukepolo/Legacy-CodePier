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
        }

        if ($this->user->subscriptions()->count()) {
            $this->user->subscription('primary')->swap(\Request::get('plan'));
        } else {
            $this->user->newSubscription('primary', \Request::get('plan'))->create(isset($cardToken) ? $cardToken->id : null);
        }

        return back();
    }

    public function getCancelSubscription()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this->user->subscription('primary')->cancel();

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
