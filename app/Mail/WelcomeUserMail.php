<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\URL; 

class WelcomeUserMail extends Mailable
{
    use Queueable;

    public $user;
    public $verificationUrl;

    public function __construct($user) {
        $this->user = $user;

        $this->verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to SeraTech - Verify Your Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
