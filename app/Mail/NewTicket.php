<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $emailMessage;
    public $email;

    public function __construct($subject, $message, $email)
    {
        $this->subject = $subject;
        $this->emailMessage = $message;
        $this->email = $email;
    }

    public function build()
    {
        return $this->from('no-reply@sgdinstitute.org')
            ->replyTo($this->email)
            ->subject($this->subject)
            ->text('emails.plain.ticket');
    }
}
