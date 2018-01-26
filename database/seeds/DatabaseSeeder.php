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
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->call(CategoriesSeeder::class);
        $this->call(BuoySeeder::class);

        $this->call(SystemSeeder::class);
        $this->call(NotificationSettings::class);
        $this->call(ServerProvidersSeeder::class);
        $this->call(RepositoryProvidersSeeder::class);
        $this->call(NotificationProvidersSeeder::class);
    }
}
