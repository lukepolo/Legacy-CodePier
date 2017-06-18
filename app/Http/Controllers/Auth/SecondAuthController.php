<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Contracts\Google2FA;

class SecondAuthController extends Controller
{
    const SECOND_AUTH_SESSION = 'second_auth';

    private $google2FA;

    /**
     * SecondAuthController constructor.
     * @param Google2FA $google2FA
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $user->update([
            'second_auth_secret' => $this->google2FA->generateSecretKey(32, config('app.key')),
            'second_auth_active' => false
        ]);

        return response()->json([
            'secret' => $user->second_auth_secret,
            'image' => $this->google2FA->getQRCodeInline(
                'CodePier',
                $user->email,
                $user->second_auth_secret
            )
        ]);
    }

    public function store(Request $request)
    {
        $valid = $this->google2FA->verifyKey(
            $request->user()->second_auth_secret,
            $request->get('token'),
            1
            // TODO - once version 2 comes out we can do the new stuff
            // https://github.com/antonioribeiro/google2fa
//            $request->user()->second_auth_updated_at
        );

        if ($valid) {

            $request->user()->update([
                'second_auth_active' => true,
                'second_auth_updated_at' => Carbon::now(),
            ]);

            Session::put(self::SECOND_AUTH_SESSION, $request->user()->second_auth_updated_at);

            if ($request->expectsJson()) {
                return response()->json($request->user());
            }

            return redirect('/');
        }

        if ($request->expectsJson()) {
            return response()->json('Token failed, try again.', 401);
        }

        return back()->withErrors('Token was rejected.');
    }

    public function show()
    {
        return view('auth.second_auth');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->update([
            'second_auth_acitve' => false,
        ]);

        return response()->json('OK');
    }
}
