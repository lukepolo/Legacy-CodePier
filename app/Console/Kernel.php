<?php

namespace App\Console;

use App\Console\Commands\CheckLifeLines;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\GetServerProviderOptions;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(CheckLifeLines::class)->everyMinute();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command(GetServerProviderOptions::class)->daily();
        $schedule->command(GetServerProviderOptions::class)->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
