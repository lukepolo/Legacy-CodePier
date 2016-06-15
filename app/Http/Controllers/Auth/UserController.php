<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * Class UserController
 * @package App\Http\Controllers\Auth
 */
class UserController extends Controller
{
    public function getMyProfile()
    {
        return view('auth.user.profile');
    }

    public function postMyProfile()
    {

    }

    public function postSshKeys()
    {

    }

    // TODO - Subscriptions

}
