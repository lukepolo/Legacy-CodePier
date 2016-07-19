<?php

namespace App\Http\Controllers;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class LandingController extends Controller
{
    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        if (\Auth::check()) {
            return view('codepier', [
                'userServerProviders' => \Auth::user()->userServerProviders,
                'servers' => \Auth::user()->servers->load('serverProvider')
            ]);
        }
        return view('landing');
    }
}
