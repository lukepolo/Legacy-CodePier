<?php

class AuthRoutes
{
    /**
     * Define the auth routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group(['as' => 'auth.', 'middleware' => ['web', 'ready'], 'prefix' => 'auth'], function (Registrar $router) {
            $router->get('login', [
                'middleware' => 'guest',
                'as'         => 'login',
                'uses'       => 'AuthController@showLogin',
            ]);

            $router->post('login', [
                'middleware' => ['guest', 'throttle:10,10'],
                'uses'       => 'AuthController@postLogin',
            ]);

            $router->get('2fa', [
                'as'   => 'two-factor',
                'uses' => 'AuthController@showTwoFactorAuth',
            ]);

            $router->post('2fa', [
                'middleware' => ['throttle:10,10'],
                'uses'       => 'AuthController@postTwoFactor',
            ]);

            $router->get('logout', [
                'as'         => 'logout',
                'uses'       => 'AuthController@logoutAction',
                'middleware' => 'auth',
            ]);

        });
    }
}
