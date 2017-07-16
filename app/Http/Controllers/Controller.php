<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ServerCommandTrait;
use App\Http\Middleware\VerifySecondAuth;
use Illuminate\Foundation\Bus\DispatchesJobs;
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
                function () {
                    return view('codepier', [
                        'user' => \Auth::user(),
                    ]);
                }
            );
        }

        if ($request->has('ref')) {
            return response()->view('landing')->withCookie(cookie('referrer', $request->input('ref')));
        }

        return view('landing');
    }
}
