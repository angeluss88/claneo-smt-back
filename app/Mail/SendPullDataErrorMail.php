<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPullDataErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SendPullDataErrorMail
    {
        return $this->subject('SEO Management Tool: Data cannot be pulled')
            ->from('no-reply@claneo.com')
            ->view('emails.sendPullDataErrorMail');
    }
}
