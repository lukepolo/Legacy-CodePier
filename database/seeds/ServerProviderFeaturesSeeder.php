<?php

use Illuminate\Database\Seeder;

class ServerProviderFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serverProviderFeatures = [
            '1' => [
                'Backups' => [
                    'cost' => '20% Monthly Total'
                ]
            ]
        ];

        foreach($serverProviderFeatures as $serverProviderID => $features) {
            foreach($features as $feature => $data) {
                \App\Models\ServerProviderFeatures::firstOrCreate([
                    'server_provider_id' => $serverProviderID,
                    'feature' => $feature,
                    'cost' => $data['cost']
                ]);
            }
        }

    }
}
