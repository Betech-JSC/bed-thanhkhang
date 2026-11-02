<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseConfirmationSlipMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public mixed $slip = null)
    {
    }

    public function build()
    {
        return $this->subject('Deprecated');
    }
}
