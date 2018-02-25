<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifySecondAuth;
use App\Traits\ServerCommandTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ServerCommandTrait;

    /**
     * @param Request $request
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|mixed
     */
    public function app(Request $request)
    {
        if (\Auth::check()) {
            return (new VerifySecondAuth())->handle(
                $request,
                function () {
                    return view('codepier', [
                        'user' => \Auth::user(),
                    ]);
                }
            );
        }

        if ($request->has('ref')) {
            return response()->view('landing.index')->withCookie(cookie('referrer', $request->input('ref')));
        }

        return view('landing.index');
    }

    public function appEventsBar(Request $request)
    {
        if (\Auth::check()) {
            return (new VerifySecondAuth())->handle(
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
