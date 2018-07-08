<?php

namespace App\Console\Commands;

use App\Models\User\User;
use App\Mail\PrivacyUpdateEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPrivacyEmailUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:privacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an email to all users saying we updated our privacy policy';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (User::all() as $user) {
            Mail::to($user->email)->send(new PrivacyUpdateEmail($user));
        }
    }
}
