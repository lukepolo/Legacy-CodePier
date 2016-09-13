<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;

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
        return response()->json(\Auth::user()->invoices());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
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
