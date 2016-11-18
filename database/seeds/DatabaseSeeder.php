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
        $this->call(NotificationSettings::class);
        $this->call(ServerProvidersSeeder::class);
        $this->call(RepositoryProvidersSeeder::class);
        $this->call(NotificationProvidersSeeder::class);
    }
}
