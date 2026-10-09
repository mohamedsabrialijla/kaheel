<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SendUserPasswordMail extends Mailable
{
    use Queueable;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to SeraTech - Credentials Your account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.send-password',
            with: [
                'user' => $this->user,
                'password' => $this->password,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
