<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User\User;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Concerns\InteractsWithAuthentication;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations, InteractsWithAuthentication;

    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('CodePier');
        });
    }

    public function testLogin()
    {
        $user = factory(User::class)->create([
            'email' => 'hello@codepier.io',
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->press('LOGIN')
                ->assertPathIs('/')
                ->waitUntil('app.$store.state.user_piles.piles.length > 0')
                ->assertSee('dev')
                ->assertSee('qa')
                ->assertSee('production');
        });
    }

    public function testEditingPile()
    {
        $this->browse(function ($browser) {
            $browser
                ->loginAs(User::first())
                ->assertPathIs('/')
                ->waitUntil('app.$store.state.user_piles.piles.length > 0')
                ->whenAvailable('.icon-pencil', function ($modal) {
                    $modal->press('.icon-pencil')
                        ->assertSee('.icon-check_circle');
                });
        });
    }
}
