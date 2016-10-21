<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Cashier\Invoice;

/**
 * Class UserSubscriptionInvoiceController.
 */
class UserSubscriptionInvoiceController extends Controller
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
        $invoices = [];

        /** @var Invoice $invoice */
        if(\Auth::user()->hasStripeId()) {
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
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        return \Auth::user()->downloadInvoice($id, [
            'vendor'  => 'CodePier',
            'product' => 'Server Management',
        ]);
    }
}
