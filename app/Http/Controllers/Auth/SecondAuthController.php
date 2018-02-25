<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SecondAuthController extends Controller
{
    const SECOND_AUTH_SESSION = 'second_auth';

    private $google2FA;

    /**
     * SecondAuthController constructor.
     *
     * @param Google2FA $google2FA
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $user->second_auth_secret = $this->google2FA->generateSecretKey(64);
        $user->second_auth_active = false;
        $user->save();

        return response()->json([
            'secret' => $user->second_auth_secret,
            'image' => $this->google2FA->getQRCodeInline(
                'CodePier',
                $user->email,
                $user->second_auth_secret
            ),
        ]);
    }

    public function store(Request $request)
    {
        $valid = $this->google2FA->verifyKeyNewer(
            $request->user()->second_auth_secret,
            $request->get('token'),
            $request->user()->second_auth_updated_at
        );

        if ($valid) {
            $request->user()->update([
                'second_auth_active' => true,
                'second_auth_updated_at' => $valid,
            ]);

            Session::put(self::SECOND_AUTH_SESSION, $valid);

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
     *
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
