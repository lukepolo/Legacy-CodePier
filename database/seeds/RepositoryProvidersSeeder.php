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
                'git_url' => 'git@github.com',
                'repository_class' => \App\Services\Server\Site\Repository\Providers\GitHub::class
            ],
            \App\Http\Controllers\Auth\OauthController::BITBUCKET => [
                'name' => 'Bitbucket',
                'url' => 'bitbucket.org',
                'git_url' => 'git@bitbucket.org',
                'repository_class' => \App\Services\Server\Site\Repository\Providers\BitBucket::class
            ],
            \App\Http\Controllers\Auth\OauthController::GITLAB => [
                'name' => 'GitLab',
                'url' => 'gitlab.com',
                'git_url' => 'git@gitlab.com',
                'repository_class' => \App\Services\Server\Site\Repository\Providers\GitLab::class
            ]
        ];

        foreach($providers as $provider => $data) {
            $providerModel = \App\Models\RepositoryProvider::firstOrCreate([
                'provider_name' => $provider,
                'name' => $data['name']
            ]);


            $providerModel->fill([
                'url' => $data['url'],
                'git_url' => $data['git_url'],
                'repository_class' => $data['repository_class']
            ]);

            $providerModel->save();
        }
    }
}
