<?php

namespace App\Http\Controllers;


class PricingController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('pricing/index');
    }
}