<?php

namespace App\Http\Controllers\User\Subscription;

use Laravel\Cashier\Invoice;
use App\Http\Controllers\Controller;

class UserSubscriptionInvoiceController extends Controller
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
        $invoices = [];

        /* @var Invoice $invoice */
        if (\Auth::user()->hasStripeId()) {
            foreach (\Auth::user()->invoices() as $invoice) {
                $invoices[] = [
                    'id' => $invoice->id,
                ];
            }
        }

        return response()->json($invoices);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        return \Auth::user()->downloadInvoice($id, [
            'vendor'  => 'CodePier',
            'product' => 'Server Management',
        ]);
    }
}
