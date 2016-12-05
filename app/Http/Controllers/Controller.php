<?php

namespace App\Http\Controllers;

use App\Models\Site\Site;
use App\Traits\ServerCommandTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ServerCommandTrait;

    public function app()
    {

        $repositoryService = new \App\Services\Repository\RepositoryService();

        $repositoryService->importSshKeyIfPrivate(Site::findOrFail(9));

        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers']),
            'runningCommands' => \Auth::user()->getRunningCommands(),
        ]);
    }
}
