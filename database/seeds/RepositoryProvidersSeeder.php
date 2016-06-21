<?php

use Illuminate\Database\Seeder;

/**
 * Class RepositoryProvidersSeeder
 */
class RepositoryProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = [
            \App\Http\Controllers\Auth\OauthController::GITHUB => [
                'name' => 'Github'
            ],
            \App\Http\Controllers\Auth\OauthController::BITBUCKET => [
                'name' => 'Bitbucket'
            ]
        ];

        foreach($providers as $provider => $data) {
            \App\Models\RepositoryProvider::firstOrCreate([
                'provider_name' => $provider,
                'name' => $data['name']
            ]);
        }
    }
}
