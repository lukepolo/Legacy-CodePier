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
            return view('dashboard');
        }
        return view('landing');
    }
}
