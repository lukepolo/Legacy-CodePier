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
                'name' => 'Github',
                'url' => 'github.com',
                'git_url' => 'git@github.com'
            ],
            \App\Http\Controllers\Auth\OauthController::BITBUCKET => [
                'name' => 'Bitbucket',
                'url' => 'bitbucket.org',
                'git_url' => 'git@bitbucket.org'
            ]
        ];

        foreach($providers as $provider => $data) {
            $providerModel = \App\Models\RepositoryProvider::firstOrCreate([
                'provider_name' => $provider,
                'name' => $data['name']
            ]);


            $providerModel->fill([
                'url' => $data['url'],
                'git_url' => $data['git_url']
            ]);

            $providerModel->save();
        }
    }
}
