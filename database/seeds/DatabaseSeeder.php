<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! \File::exists(storage_path('oauth-private.key'))) {
            Artisan::call('passport:install');
        }

        $this->call(BuoySeeder::class);
        $this->call(NotificationSettings::class);
        $this->call(ServerProvidersSeeder::class);
        $this->call(RepositoryProvidersSeeder::class);
        $this->call(NotificationProvidersSeeder::class);
    }
}
