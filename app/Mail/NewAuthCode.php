<?php

namespace App\Mail;

use App\Models\AuthCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewAuthCode extends Mailable
{
    use Queueable, SerializesModels;

    public $authCode;

    /**
     * Create a new message instance.
     *
     * @param AuthCode $authCode
     */
    public function __construct(AuthCode $authCode)
    {
        $this->authCode = $authCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('luke@codepier.io')
            ->subject('You have been invited to test CodePier.io')
            ->view('emails.codePierInvite');
    }
}

