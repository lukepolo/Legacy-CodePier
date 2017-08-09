<?php

namespace App\Console;

use App\Console\Commands\CheckLifeLines;
use App\Console\Commands\SendBetaEmails;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ReleasedNewVersion;
use App\Console\Commands\TestMonitorScripts;
use App\Console\Commands\ClearFailedCommands;
use App\Console\Commands\StartDevEnvironment;
use App\Console\Commands\ProvisionDevEnvironment;
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
        CheckLifeLines::class,

        // REACTIVITY TESTS
        ServerCommandUpdated::class,

        ProvisionDevEnvironment::class,
        StartDevEnvironment::class,

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
