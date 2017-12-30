<?php

namespace App\Http\Controllers\User;

use App\Models\User\User;
use App\Mail\ConfirmWelcome;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class UserConfirmController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param $code
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($code)
    {
        $userId = Hashids::connection('users')->decode($code);
        if (empty($userId)) {
            return redirect('/')->withErrors('Sorry, this confirmation is invalid');
        }
        $user = User::findOrFail($userId[0]);

        if (! $user->confirmed) {
            $user->update([
                'confirmed' => true,
            ]);

            return redirect('/')->withSuccess('You have confirmed your email');
        }

        return redirect('/')->withErrors('You have already confirmed your account');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $user = \Auth::user();

        Mail::to($user)->send(new ConfirmWelcome($user));

        return response()->json('OK');
    }
}
