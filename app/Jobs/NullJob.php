<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NullJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    public $tries = 1;
    public $timeout = 90;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle()
    {

    }
}
