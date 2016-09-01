<?php

namespace App\SocialProviders;

/**
 * Class TokenData.
 */
class TokenData
{
    public $token;
    public $userID;

    /**
     * TokenData constructor.
     *
     * @param $token
     * @param $userID
     */
    public function __construct($token, $userID)
    {
        $this->token = $token;
        $this->userID = $userID;
    }
}
