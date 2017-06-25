<?php

use Illuminate\Database\Seeder;

/**
 * Class ServerProvidersSeeder.
 */
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
                'name'     => 'Digital Ocean',
                'features' => [
                    ['feature' => 'Backups', 'cost' => '20% Monthly Total', 'default' => false, 'option' => 'backups'],
                    ['feature' => 'IPV6', 'cost' => null, 'default' => true, 'option' => 'ipv6'],
                    ['feature' => 'Private Networking', 'cost' => null, 'default' => true, 'option' => 'privateNetworking'],
                ],
                'class' => \App\Services\Server\Providers\DigitalOceanProvider::class,
                'oauth' => true,
                'secret_token' => false,
            ],
            \App\Http\Controllers\Server\Providers\Linode\LinodeController::LINODE => [
                'name'     => 'Linode',
                'features' => [],
                'class' => \App\Services\Server\Providers\LinodeProvider::class,
                'oauth' => false,
                'secret_token' => false,
            ],
            'custom' => [
                'name'     => 'Custom Provider',
                'features' => [],
                'class' => \App\Services\Server\Providers\CustomProvider::class,
                'oauth' => false,
                'secret_token' => true,
            ],
        ];

        foreach ($providers as $provider => $data) {
            $serverProvider = \App\Models\Server\Provider\ServerProvider::firstOrNew([
                'provider_class' => $data['class'],
            ]);

            $serverProvider->fill([
                'provider_name' => $provider,
                'name'          => $data['name'],
                'oauth'         => $data['oauth'],
                'token'         => $data['token'],
                'secret_token'        => $data['secret_token'],
            ]);

            $serverProvider->save();

            foreach ($data['features'] as $feature) {
                $serverFeature = \App\Models\Server\Provider\ServerProviderFeatures::firstOrNew([
                    'server_provider_id' => $serverProvider->id,
                    'feature'            => $feature['feature'],
                ]);

                $serverFeature->fill([
                    'option'  => $feature['option'],
                    'cost'    => $feature['cost'],
                    'default' => $feature['default'],
                ]);

                $serverFeature->save();
            }
        }
    }
}
