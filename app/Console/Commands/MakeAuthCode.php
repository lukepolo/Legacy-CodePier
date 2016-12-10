<?php

namespace App\Console\Commands;

use App\Mail\NewAuthCode;
use App\Models\AuthCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MakeAuthCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:code {email? : The email address you want to send it to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a new auth code to a user or outputs to console';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');

        $authCode = AuthCode::create([
            'email' => $email,
            'code' => str_random()
        ]);

        Mail::to($email)->send(new NewAuthCode($authCode));
    }
}
