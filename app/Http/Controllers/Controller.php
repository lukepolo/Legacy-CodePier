<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifySecondAuth;
use App\Traits\ServerCommandTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ServerCommandTrait;

    public function app(Request $request)
    {
        if (\Auth::check()) {
            return (new VerifySecondAuth)->handle(
                $request,
                function() {
                    return view('codepier', [
                        'user' => \Auth::user(),
                    ]);
                }
            );
        }

        return view('landing');
    }
}
