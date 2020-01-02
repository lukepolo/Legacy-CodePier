<?php

namespace App\Console\Commands;

use App\Jobs\CheckQueues;
use Illuminate\Console\Command;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

class PingLifeLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ping:lifelines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pings Lifelines';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new CheckQueues());

        $httpClient = new HttpClient;

        $httpClient->get('https://lifeline.codepier.io/JOY32EwMR769mp5ynqXej6eAkxLjl4VZdvQGB1Wg');

        try {
            $httpClient->get('https://ws.codepier.io:6001/');
        } catch (RequestException $e) {
            if ($e->getCode() === 404) {
                $httpClient->get('https://lifeline.codepier.io/WRrjm57nZVqpB20l4xPDG1e36KAMYJQgdwaOoXEk');
            }
        }

        try {
            $httpClient->post('http://dns.codepier.io/register');
            $httpClient->get('https://lifeline.codepier.io/rvAdlMBaZqgpo46EbjYNxAe73XVPGJLK1xkRym5W');
        } catch (RequestException $e) {
            // DO NOTHING
        }
    }
}
