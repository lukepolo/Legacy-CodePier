<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract;
use App\Http\Requests;
use App\Models\UserServer;

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
        dd($this->serverService->provision(UserServer::find(6)));

        if (\Auth::check()) {
            return view('dashboard', [
                'serverProviders' => \Auth::user()->serverProviders,
                'servers' => \Auth::user()->servers
            ]);
        }
        return view('landing');
    }
}
