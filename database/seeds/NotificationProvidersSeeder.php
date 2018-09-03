<?php

use Illuminate\Database\Seeder;

class NotificationProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = [
            \App\Http\Controllers\Auth\OauthController::SLACK => [
                'name' => 'Slack',
                'connection_type' => \App\Http\Controllers\Auth\Providers\NotificationProvidersController::OAUTH,
            ],
            \App\Http\Controllers\Auth\Providers\NotificationProvidersController::DISCORD => [
                'name' => 'Discord',
                'connection_type' => \App\Http\Controllers\Auth\Providers\NotificationProvidersController::WEBHOOK,
            ],
        ];

        foreach ($providers as $provider => $data) {
            \App\Models\NotificationProvider::firstOrCreate([
                'provider_name'   => $provider,
                'name'            => $data['name'],
                'connection_type' => $data['connection_type'],
            ]);
        }
    }
}
