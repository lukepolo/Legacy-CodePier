<?php

namespace App\Http\Controllers;

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
        if(\Auth::check()) {
            return view('codepier', [
                'user' => \Auth::user(),
            ]);
        }

        return view('landing');

    }
}
