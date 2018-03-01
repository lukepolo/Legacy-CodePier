<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Site\Lifeline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Notifications\LifeLineThresholdExceeded;

class ClearAppCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:app-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the app caches';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Cache::flush('app.services');
    }
}
