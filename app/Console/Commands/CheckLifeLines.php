<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Site\Lifeline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Notifications\LifeLineThresholdExceeded;

class CheckLifeLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:lifelines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks lifelines and sends notifications if needed';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lifelineModel = new Lifeline();

        DB::table('lifelines')
            ->selectRaw('id, DATE_ADD(last_seen, INTERVAL threshold MINUTE) as threshold')
            ->where('sent_notifications', '<', 3)
            ->having('threshold', '<', Carbon::now()->subSeconds(15))
            ->orderBy('id')
                ->chunk(1000, function ($lifelines) use($lifelineModel) {
                foreach ($lifelines as $lifeline) {
                    $lifelineModel->id = $lifeline->id;
                    $lifelineModel->notify(new LifeLineThresholdExceeded);
                }
            });
    }
}
