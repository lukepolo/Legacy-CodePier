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
            ],
        ];

        foreach ($providers as $provider => $data) {
            \App\Models\NotificationProvider::firstOrCreate([
                'provider_name' => $provider,
                'name'          => $data['name'],
            ]);
        }
    }
}
