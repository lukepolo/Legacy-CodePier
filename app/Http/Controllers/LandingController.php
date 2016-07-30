<?php

namespace App\Http\Controllers;

use App\Models\Server;

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
                'servers' => Server::with('serverProvider')->get()
            ]);
        }
        return view('landing');
    }
}
