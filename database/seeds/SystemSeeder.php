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
        foreach (\App\Services\Systems\SystemService::PROVISION_SYSTEMS as $system => $systemClass) {
            $systemModel = \App\Models\System::firstOrNew([
                'name' => ucwords($system),
            ]);

            $systemModel->fill([
                'class' => $system
            ]);

            $systemModel->save();
        }
    }
}
