<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract;
use App\Http\Requests;
use App\Models\ServerProvider;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class LandingController extends Controller
{
    /**
     * LandingController constructor.
     */
    public function __construct(ServerServiceContract $serverServiceContract)
    {
        $this->serverService = $serverServiceContract;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        if (\Auth::check()) {
            return view('dashboard', [
                'userServerProviders' => \Auth::user()->userServerProviders,
                'servers' => \Auth::user()->servers
            ]);
        }
        return view('landing');
    }
}
