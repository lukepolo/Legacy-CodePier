<?php

namespace App\Console\Commands;

use App\Mail\BetaInvite;
use App\Models\BetaEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBetaEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'betaEmails:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends all the beta emails to the users';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dd(Mail::to('lpolicinski@gmail.com')->send(new BetaInvite()));
    }
}
