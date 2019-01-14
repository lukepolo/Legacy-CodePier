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
                    ['feature' => 'IPV6', 'cost' => null, 'default' => true, 'option' => 'ipv6'],
                    ['feature' => 'Monitoring', 'cost' => null, 'default' => true, 'option' => 'monitoring'],
                    ['feature' => 'Private Networking', 'cost' => null, 'default' => true, 'option' => 'privateNetworking'],
                    ['feature' => 'Backups', 'cost' => '20% Monthly Total', 'default' => false, 'option' => 'backups'],
                ],
                'class' => \App\Services\Server\Providers\DigitalOceanProvider::class,
                'secret_token' => false
            ],
            \App\Http\Controllers\Server\Providers\Linode\LinodeController::LINODE => [
                'name'     => 'Linode',
                'features' => [],
                'class' => \App\Services\Server\Providers\LinodeProvider::class,
                'secret_token' => false
            ],
            'custom' => [
                'name'     => 'Custom Provider',
                'features' => [],
                'class' => \App\Services\Server\Providers\CustomProvider::class,
                'secret_token' => true
            ],
            \App\Http\Controllers\Server\Providers\Vultr\VultrController::VULTR => [
                'name'     => 'Vultr',
                'features' => [
                    ['feature' => 'IPV6', 'cost' => null, 'default' => true, 'option' => 'enable_ipv6'],
                    ['feature' => 'Private Networking', 'cost' => null, 'default' => true, 'option' => 'enable_private_network'],
                    ['feature' => 'Backups', 'cost' => '20% Monthly Total', 'default' => false, 'option' => 'auto_backups'],
                    ['feature' => 'DDOS Protection', 'cost' => '$10 a month', 'default' => false, 'option' => 'ddos_protection'],
                ],
                'class' => \App\Services\Server\Providers\VultrProvider::class,
                'secret_token' => false
            ],
        ];

        foreach ($providers as $provider => $data) {
            $serverProvider = \App\Models\Server\Provider\ServerProvider::firstOrNew([
                'provider_class' => $data['class'],
            ]);

            $serverProvider->fill([
                'provider_name'     => $provider,
                'name'              => $data['name'],
                'secret_token'      => $data['secret_token']
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
