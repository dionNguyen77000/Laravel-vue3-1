<?php

namespace App\Mail;

use Carbon\Traits\Serialization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    // build the message
    public function build()
    {
        # code...
        return $this->subject('Test Mail From Golden lor Yarrabilba')->view('emails.TestMail');
    }

}

