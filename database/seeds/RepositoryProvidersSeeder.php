<?php

use Illuminate\Database\Seeder;

/**
 * Class RepositoryProvidersSeeder.
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
                'name'             => 'GitHub',
                'url'              => 'github.com',
                'git_url'          => 'git@github.com',
                'commit_url'       => 'commit',
                'repository_class' => \App\Services\Repository\Providers\GitHub::class,
            ],
            \App\Http\Controllers\Auth\OauthController::BITBUCKET => [
                'name'             => 'Bitbucket',
                'url'              => 'bitbucket.org',
                'git_url'          => 'git@bitbucket.org',
                'commit_url'       => 'commits',
                'repository_class' => \App\Services\Repository\Providers\BitBucket::class,
            ],
            \App\Http\Controllers\Auth\OauthController::GITLAB => [
                'name'             => 'GitLab',
                'url'              => 'gitlab.com',
                'git_url'          => 'git@gitlab.com',
                'commit_url'       => 'commit',
                'repository_class' => \App\Services\Repository\Providers\GitLab::class,
            ],
        ];

        foreach ($providers as $provider => $data) {
            $providerModel = \App\Models\RepositoryProvider::firstOrNew([
                'repository_class' => $data['repository_class'],
            ]);

            $providerModel->fill([
                'provider_name' => $provider,
                'name'          => $data['name'],
                'url'              => $data['url'],
                'git_url'          => $data['git_url'],
                'commit_url'       => $data['commit_url'],
            ]);

            $providerModel->save();
        }
    }
}
