<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckQueues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $httpClient = new HttpClient;
        $httpClient->get('https://lifeline.codepier.io/oZr20qP1O4xd67RYALVDAnNWaBk3ygb9mGKwEnJ5');
    }
}
