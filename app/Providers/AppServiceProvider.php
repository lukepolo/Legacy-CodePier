<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Pile;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::created(function ($user) {
            // lets create some piles for them
            $defaultPiles = [
                'dev',
                'qa',
                'production'
            ];
            foreach($defaultPiles as $defaultPile) {
                Pile::create([
                    'name' => $defaultPile,
                    'user_id' => $user->id
                ]);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
