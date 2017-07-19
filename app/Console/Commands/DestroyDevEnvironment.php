<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DestroyDevEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroys the provisioned CodePier development VM in order to start with a clean slate.';

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
        $process = new Process('cd storage/dev-environment && vagrant destroy');
        $process->setTimeout(0);

        return $process->run();
    }
}
