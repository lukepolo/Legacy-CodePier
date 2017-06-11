<?php

namespace App\Console;

use App\Console\Commands\SendBetaEmails;
use App\Console\Commands\ServeDevEnvironment;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ReleasedNewVersion;
use App\Console\Commands\TestMonitorScripts;
use App\Console\Commands\ClearFailedCommands;
use App\Console\Commands\GetServerProviderOptions;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Tests\ServerEvents\ServerCommandUpdated;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ReleasedNewVersion::class,
        TestMonitorScripts::class,
        ClearFailedCommands::class,
        GetServerProviderOptions::class,

        // REACTIVITY TESTS
        ServerCommandUpdated::class,

        // BETA - Emails - TEMP
        SendBetaEmails::class,

        ServeDevEnvironment::class,
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
