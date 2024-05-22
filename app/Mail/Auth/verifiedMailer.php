<?php

namespace App\Mail\auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class verifiedMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $appName;
    public $verificationUrl;
    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->appName = config('app.name');
        $this->verificationUrl = config('app.urlFront'); //agregar ruta aqui
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a '.$this->appName.', es necesario verificar tu cuenta.',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.verified',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Bienvenido a '.$this->appName.', es necesario verificar tu cuenta.')
                    ->view('mails.verified')
                    ->with([
                        'user' => $this->user,
                        'appName' => $this->appName,
                        'verificationUrl' => $this->verificationUrl,
                    ]);;
    }
}
