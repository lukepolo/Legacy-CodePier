<?php

namespace App\Console;

use App\Console\Commands\MakeAuthCode;
use App\Console\Commands\Tests\ServerEvents\ServerCommandUpdated;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ReleasedNewVersion;
use App\Console\Commands\TestMonitorScripts;
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
        MakeAuthCode::class,
        ReleasedNewVersion::class,
        TestMonitorScripts::class,
        GetServerProviderOptions::class,

        // REACTIVITY TESTS
        ServerCommandUpdated::class
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
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
