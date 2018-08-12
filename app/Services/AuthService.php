<?php

namespace App\Services;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Encryption\Encrypter;

class AuthService
{
    private $encrypter;

    /**
     * OauthController constructor.
     * @param Encrypter $encrypter
     */
    public function __construct(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function generateJwtToken()
    {
        return $this->encrypter->encrypt(JWT::encode([
            'sub' => \Auth::user()->id,
            'csrf' => csrf_token(),
            'expiry' => Carbon::now()->addMinutes(config('session.lifetime')),
        ], $this->encrypter->getKey()));
    }
}
