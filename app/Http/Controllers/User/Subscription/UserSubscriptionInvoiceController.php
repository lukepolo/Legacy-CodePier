<?php

namespace App\Http\Controllers\User\Subscription;

use App\Models\User\User;
use Illuminate\Http\Request;
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
                    'date' => $invoice->date(),
                    'total' => $invoice->rawTotal(),
                ];
            }
        }

        return response()->json($invoices);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, $id)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        /** @var User $user */
        $user = $request->user();

        return $user->downloadInvoice($id, [
            'vendor'  => 'CodePier LLC',
            'product' => $user->getSubscriptionName(),
        ]);
    }
}
