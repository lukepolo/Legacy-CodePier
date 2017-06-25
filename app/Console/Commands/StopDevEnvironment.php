<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StopDevEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stops the active CodePier dev VM.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $process = new Process('cd storage/dev-environment && vagrant stop');
        $process->setTimeout(0);

        return $process->run();
    }
}
