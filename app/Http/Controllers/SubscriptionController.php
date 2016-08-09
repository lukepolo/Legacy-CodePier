<?php

namespace App\Http\Controllers;

use Stripe\Plan;
use Stripe\Stripe;

/**
 * Class SubscriptionController
 * @package App\Http\Controllers
 */
class SubscriptionController extends Controller
{
    /**
     * ServerOptionController constructor.
     */
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\Cache::rememberForever('plans', function () {
            return collect(Plan::all()->data)->sortBy('metadata.order');
        }));
    }
}
