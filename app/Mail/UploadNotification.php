<?php

namespace App\Mail;

use App\Models\UploadSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UploadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $session;

    public function __construct(UploadSession $session)
    {
        $this->session = $session;
    }

    public function build()
    {
        return $this->subject('Your File Upload Link')
                    ->view('emails.upload_notification');
    }
}