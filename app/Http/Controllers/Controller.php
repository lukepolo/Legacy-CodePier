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

    public function welcome(Request $request)
    {
        if ($request->has('ref')) {
            return response()->view('landing.index')->withCookie(cookie('referrer', $request->input('ref')));
        }

        return view('landing.index');
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|mixed
     */
    public function app(Request $request)
    {
        return (new VerifySecondAuth)->handle(
            $request,
            function () {
                return view('codepier', [
                    'user' => \Auth::user(),
                ]);
            }
        );
    }

    public function appEventsBar(Request $request)
    {
        if (\Auth::check()) {
            return (new VerifySecondAuth)->handle(
                $request,
                function () {
                    return view('eventsBar', [
                        'user' => \Auth::user(),
                    ]);
                }
            );
        }

        return response()->redirect('/login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectToApp()
    {
        return redirect(config('app.url'));
    }
}
