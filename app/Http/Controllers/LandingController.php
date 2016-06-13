<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Http\Requests;
use App\Models\Server;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class LandingController extends Controller
{
    /**
     * LandingController constructor.
     */
    public function __construct(ServerServiceContract $serverServiceContract, SiteServiceContract $siteServiceContract)
    {
        $this->serverService = $serverServiceContract;
        $this->siteService = $siteServiceContract;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        if (\Auth::check()) {

//            $server = Server::find(5);

//            if($this->serverService->provision($server)) {
//                if($this->siteService->create($server)) {
//                    $this->siteService->deploy($server);
//                }
//            }
            return view('dashboard', [
                'serverProviders' => \Auth::user()->serverProviders,
                'servers' => \Auth::user()->servers
            ]);
        }
        return view('landing');
    }
}
