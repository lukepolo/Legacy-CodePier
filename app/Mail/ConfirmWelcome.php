<?php

namespace App\Mail;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Welcome constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Please Confirm Your Email')->from('hello@codepier.io', 'CodePier')
            ->markdown('mail.confirm-welcome');
    }
}
