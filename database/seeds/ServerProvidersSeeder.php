<?php

use Illuminate\Database\Seeder;

class ServerProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = [
            \App\Http\Controllers\Auth\OauthController::DIGITAL_OCEAN => [
                'name' => 'Digital Ocean'
            ]
        ];

        foreach($providers as $provider => $data) {
            \App\Models\ServerProvider::firstOrCreate([
                'provider_name' => $provider,
                'name' => $data['name']
            ]);
        }

    }
}
