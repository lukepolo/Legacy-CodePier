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

    public function direct()
    {
        if (\Auth::check()) {
            return view('codepier', [
                'user' => \Auth::user()->load(['teams', 'piles.servers']),
                'runningCommands' => \Auth::user()->getRunningCommands(),
            ]);
        }

        return redirect('/login');
    }
}
