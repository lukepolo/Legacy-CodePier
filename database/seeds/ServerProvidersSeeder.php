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
                'name'     => 'Digital Ocean',
                'features' => [
                    ['feature' => 'Backups', 'cost' => '20% Monthly Total', 'default' => false, 'option' => 'backups'],
                    ['feature' => 'IPV6', 'cost' => null, 'default' => true, 'option' => 'ipv6'],
                    ['feature' => 'Private Networking', 'cost' => null, 'default' => true, 'option' => 'privateNetworking'],
                ],
                'class' => \App\Services\Server\Providers\DigitalOceanProvider::class,
            ],
        ];

        foreach ($providers as $provider => $data) {
            $serverProvider = \App\Models\ServerProvider::firstOrCreate([
                'provider_name' => $provider,
                'name'          => $data['name'],
                'provider_class' => $data['class'],
            ]);

            foreach ($data['features'] as $feature) {
                $serverFeature = \App\Models\ServerProviderFeatures::firstOrNew([
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
