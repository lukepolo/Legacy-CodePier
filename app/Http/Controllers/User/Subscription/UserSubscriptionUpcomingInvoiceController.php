<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;

class UserSubscriptionUpcomingInvoiceController extends Controller
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
        $invoice = \Auth::user()->upcomingInvoice();

        return response()->json(! empty($invoice) ? $invoice->asStripeInvoice() : null);
    }
}
