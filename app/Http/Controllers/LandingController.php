<?php

namespace App\Http\Controllers;

use App\Http\Requests;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class LandingController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        if (\Auth::check()) {
            return view('dashboard', [
                'serverProviders' => \Auth::user()->serverProviders,
                'servers' => \Auth::user()->servers
            ]);
        }
        return view('landing');
    }
}
