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
            \App\Http\Controllers\Server\Providers\DigitalOcean\DigitalOceanController::DIGITALOCEAN => [
                'name'     => 'Digital Ocean',
                'features' => [
                    ['feature' => 'Backups', 'cost' => '20% Monthly Total', 'default' => false, 'option' => 'backups'],
                    ['feature' => 'IPV6', 'cost' => null, 'default' => true, 'option' => 'ipv6'],
                    ['feature' => 'Monitoring', 'cost' => null, 'default' => true, 'option' => 'monitoring'],
                    ['feature' => 'Private Networking', 'cost' => null, 'default' => true, 'option' => 'privateNetworking'],
                ],
                'class' => \App\Services\Server\Providers\DigitalOceanProvider::class,
                'oauth' => false,
                'secret_token' => false,
                'multiple_accounts' => true
            ],
            \App\Http\Controllers\Server\Providers\Linode\LinodeController::LINODE => [
                'name'     => 'Linode',
                'features' => [],
                'class' => \App\Services\Server\Providers\LinodeProvider::class,
                'oauth' => false,
                'secret_token' => false,
                'multiple_accounts' => false
            ],
            'custom' => [
                'name'     => 'Custom Provider',
                'features' => [],
                'class' => \App\Services\Server\Providers\CustomProvider::class,
                'oauth' => false,
                'secret_token' => true,
                'multiple_accounts' => false
            ],
            \App\Http\Controllers\Server\Providers\Vultr\VultrController::VULTR => [
                'name'     => 'Vultr',
                'features' => [],
                'class' => \App\Services\Server\Providers\VultrProvider::class,
                'oauth' => false,
                'secret_token' => false,
                'multiple_accounts' => false
            ],
        ];

        foreach ($providers as $provider => $data) {
            $serverProvider = \App\Models\Server\Provider\ServerProvider::firstOrNew([
                'provider_class' => $data['class'],
            ]);

            $serverProvider->fill([
                'provider_name'     => $provider,
                'name'              => $data['name'],
                'oauth'             => $data['oauth'],
                'secret_token'      => $data['secret_token'],
                'multiple_accounts' => $data['multiple_accounts']
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
