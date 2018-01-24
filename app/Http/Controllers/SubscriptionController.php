<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(SubscriptionPlan::orderBy('amount')->get());
    }
}
