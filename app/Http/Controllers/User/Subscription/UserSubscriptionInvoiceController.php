<?php

namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Token;

/**
 * Class UserSubscriptionInvoiceController
 * @package App\Http\Controllers
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
     * @param $userId
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        return response()->json(User::findOrfail($userId)->invoices());
    }

    /**
     * Display the specified resource.
     *
     * @param $userId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($userId, $id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        return User::findOrfail($userId)->downloadInvoice($id, [
            'vendor' => 'CodePier',
            'product' => 'Server Management',
        ]);
    }
}
