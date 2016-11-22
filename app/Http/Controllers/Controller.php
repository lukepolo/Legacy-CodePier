<?php

namespace App\Http\Controllers;

use App\Traits\ServerCommandTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ServerCommandTrait;

//
//    /**
//     * Controller constructor.
//     */
//    public function __construct()
//    {
//        $site = \App\Models\Site\Site::has('servers')->first();
//
//        event(new \App\Events\Site\DeploymentCompleted($site, $site->servers->first(), $site->deployments->last()->serverDeployments->last()));
//    }
}
