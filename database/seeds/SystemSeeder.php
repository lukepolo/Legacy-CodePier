<?php

use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var \App\Services\Systems\SystemService $systemService */
        $systemService = app(\App\Contracts\Systems\SystemServiceContract::class);
        foreach (\App\Services\Systems\SystemService::PROVISION_SYSTEMS as $system => $systemClass) {
            \App\Models\System::firstOrCreate([
                'name' => ucwords($system),
            ]);
        }
    }
}
