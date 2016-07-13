<?php

namespace App\Http\Controllers;

use App\Events\Server\ServerProvisionStatusChanged;
use App\Http\Requests;
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
                'servers' => \Auth::user()->servers->load('serverProvider')
            ]);
        }
        return view('landing');
    }
}
